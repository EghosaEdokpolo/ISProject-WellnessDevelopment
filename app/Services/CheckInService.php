<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CheckIn;
use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CheckInService
{
    public function processVenueQrScan(User $user, Event $event): array
    {
        $this->assertWithinCheckInWindow($event);

        try {
            $result = DB::transaction(function () use ($user, $event): array {
                $lockedEvent = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();

                $registration = Registration::with(['waiver', 'checkIn', 'wellbeingResponses'])
                    ->where('user_id', $user->id)
                    ->where('event_id', $lockedEvent->id)
                    ->lockForUpdate()
                    ->first();

                $autoRegistered = false;
                $alreadyCheckedIn = false;

                if (! $registration) {
                    if (! config('wellloop.checkin.allow_walk_in_qr', true)) {
                        throw new RuntimeException('Walk-in QR registration is disabled for this session. Please register online first.');
                    }

                    $capacity = (int) ($lockedEvent->capacity ?? 0);
                    if ($capacity > 0) {
                        $currentCount = Registration::where('event_id', $lockedEvent->id)->count();
                        if ($currentCount >= $capacity) {
                            throw new RuntimeException('This session has reached its maximum venue capacity (' . $capacity . ' participants).');
                        }
                    }

                    $registration = Registration::create([
                        'user_id'       => $user->id,
                        'event_id'      => $lockedEvent->id,
                        'status'        => 'checked_in',
                        'consent_given' => true,
                    ]);
                    $autoRegistered = true;
                }

                if ($registration->checkIn) {
                    $alreadyCheckedIn = true;
                } else {
                    CheckIn::create([
                        'registration_id' => $registration->id,
                        'checked_in_at'   => now(),
                        'method'          => 'qr_scan',
                    ]);

                    $registration->update(['status' => 'checked_in']);
                }

                $points = (int) ($lockedEvent->points_checkin ?? config('wellloop.checkin.default_points', 20));
                $pointsAwarded = $this->awardPointsOnce($registration, 'attended_event', $points);

                return [
                    'registration'       => $registration->fresh(['event', 'waiver', 'checkIn', 'wellbeingResponses']),
                    'auto_registered'    => $autoRegistered,
                    'already_checked_in' => $alreadyCheckedIn,
                    'points_earned'      => $pointsAwarded ? $points : 0,
                ];
            });
        } catch (UniqueConstraintViolationException) {
            $registration = Registration::with(['event', 'waiver', 'checkIn', 'wellbeingResponses'])
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $result = [
                'registration'       => $registration,
                'auto_registered'    => false,
                'already_checked_in' => true,
                'points_earned'      => 0,
            ];
        }

        $this->invalidateAnalyticsCache($event);

        Log::info('WellLoop venue QR check-in processed', [
            'user_id'            => $user->id,
            'staff_id'           => $user->staff_id,
            'event_id'           => $event->id,
            'registration_id'    => $result['registration']->id,
            'auto_registered'    => $result['auto_registered'],
            'already_checked_in' => $result['already_checked_in'],
            'points_earned'      => $result['points_earned'],
        ]);

        $registration = $result['registration'];
        $hasPreWho5 = $registration->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isNotEmpty();

        return [
            ...$result,
            'needs_onboarding' => ! $registration->waiver || ! $hasPreWho5,
        ];
    }

    public function confirmRegistrationCheckIn(Registration $registration, string $method = 'qr_scan', ?User $actor = null): array
    {
        $registration->load(['event', 'user', 'waiver', 'checkIn']);
        $event = $registration->event;
        $attendee = $registration->user;

        if (! $actor || ! $actor->isHrCoordinator()) {
            $this->assertWithinCheckInWindow($event);
        }

        if (! $registration->waiver) {
            throw new RuntimeException('Mandatory event waiver has not been signed yet. Please complete the waiver first.');
        }

        if ($registration->checkIn) {
            return [
                'name'               => $attendee->name,
                'event'              => $event->name,
                'points'             => 0,
                'already_checked_in' => true,
            ];
        }

        $points = (int) ($event->points_checkin ?? config('wellloop.checkin.default_points', 20));

        DB::transaction(function () use ($registration, $method, $points): void {
            $lockedReg = Registration::with('checkIn')->whereKey($registration->id)->lockForUpdate()->firstOrFail();

            if ($lockedReg->checkIn) {
                return;
            }

            CheckIn::create([
                'registration_id' => $lockedReg->id,
                'checked_in_at'   => now(),
                'method'          => $method,
            ]);

            $lockedReg->update(['status' => 'checked_in']);

            $this->awardPointsOnce($lockedReg, 'attended_event', $points);
        });

        $this->invalidateAnalyticsCache($event);

        return [
            'name'               => $attendee->name,
            'event'              => $event->name,
            'points'             => $points,
            'already_checked_in' => false,
        ];
    }

    public function assertWithinCheckInWindow(Event $event): void
    {
        $tz                 = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $opensBeforeMinutes = (int) config('wellloop.checkin.opens_minutes_before', 60);
        $closesAfterHours   = (int) config('wellloop.checkin.closes_hours_after', 8);

        $eventStart  = $event->starts_at->copy()->shiftTimezone($tz);
        $windowStart = $eventStart->copy()->subMinutes($opensBeforeMinutes);
        $windowEnd   = $eventStart->copy()->addHours($closesAfterHours);
        $now         = now($tz);

        if ($now->lt($windowStart)) {
            throw new RuntimeException(
                'Check-in for ' . $event->name . ' opens 1 hour before the session on ' . $windowStart->format('D, d M Y \a\t H:i') . ' EAT.'
            );
        }

        if ($now->gt($windowEnd)) {
            throw new RuntimeException(
                'The check-in window for ' . $event->name . ' has closed. Contact People & Culture for attendance assistance.'
            );
        }
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): bool
    {
        if ($points <= 0) {
            return false;
        }

        $alreadyAwarded = PointsTransaction::where('registration_id', $registration->id)
            ->where('reason', $reason)
            ->exists();

        if ($alreadyAwarded) {
            return false;
        }

        PointsTransaction::create([
            'user_id'         => $registration->user_id,
            'registration_id' => $registration->id,
            'reason'          => $reason,
            'points'          => $points,
        ]);

        $registration->user->increment('points_balance', $points);

        return true;
    }

    private function invalidateAnalyticsCache(Event $event): void
    {
        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));

        $impactPrefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($impactPrefix);

        if ($event->category) {
            Cache::forget($impactPrefix . $event->category);
        }
    }
}