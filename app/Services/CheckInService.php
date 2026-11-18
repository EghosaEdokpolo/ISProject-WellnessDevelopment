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
    /**
     * Calculate exact check-in timing status and points (Full vs. 10-Min Late Grace vs. Closed).
     *
     * @return array{
     *     status: string,
     *     is_open: bool,
     *     is_late: bool,
     *     minutes_late: int,
     *     base_points: int,
     *     awarded_points: int,
     *     points_reduced_by: int,
     *     penalty_percent: int,
     *     opens_at: \Illuminate\Support\Carbon,
     *     starts_at: \Illuminate\Support\Carbon,
     *     closes_at: \Illuminate\Support\Carbon
     * }
     */
    public function evaluateGateStatus(Event $event): array
    {
        $tz                 = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $opensBeforeMinutes = (int) config('wellloop.checkin.opens_minutes_before', 60);
        $lateGraceMinutes   = (int) config('wellloop.checkin.late_grace_minutes', 10);
        $penaltyPercent     = (int) config('wellloop.checkin.late_penalty_percent', 40);
        $basePoints         = (int) ($event->points_checkin ?? config('wellloop.checkin.default_points', 25));

        $startsAt = $event->starts_at->copy()->shiftTimezone($tz);
        $opensAt  = $startsAt->copy()->subMinutes($opensBeforeMinutes);
        $closesAt = $startsAt->copy()->addMinutes($lateGraceMinutes);
        $now      = now($tz);

        if ($now->lt($opensAt)) {
            return [
                'status'            => 'locked',
                'is_open'           => false,
                'is_late'           => false,
                'minutes_late'      => 0,
                'base_points'       => $basePoints,
                'awarded_points'    => $basePoints,
                'points_reduced_by' => 0,
                'penalty_percent'   => $penaltyPercent,
                'opens_at'          => $opensAt,
                'starts_at'         => $startsAt,
                'closes_at'         => $closesAt,
            ];
        }

        if ($now->gt($closesAt)) {
            return [
                'status'            => 'closed',
                'is_open'           => false,
                'is_late'           => false,
                'minutes_late'      => 0,
                'base_points'       => $basePoints,
                'awarded_points'    => 0,
                'points_reduced_by' => $basePoints,
                'penalty_percent'   => $penaltyPercent,
                'opens_at'          => $opensAt,
                'starts_at'         => $startsAt,
                'closes_at'         => $closesAt,
            ];
        }

        $isLate      = $now->gt($startsAt);
        $minutesLate = $isLate ? max(1, (int) $startsAt->diffInMinutes($now)) : 0;

        if ($isLate && $basePoints > 0) {
            $awardedPoints   = max(1, (int) round($basePoints * ((100 - $penaltyPercent) / 100)));
            $pointsReducedBy = max(0, $basePoints - $awardedPoints);
        } else {
            $awardedPoints   = $basePoints;
            $pointsReducedBy = 0;
        }

        return [
            'status'            => $isLate ? 'late_grace' : 'open_on_time',
            'is_open'           => true,
            'is_late'           => $isLate,
            'minutes_late'      => $minutesLate,
            'base_points'       => $basePoints,
            'awarded_points'    => $awardedPoints,
            'points_reduced_by' => $pointsReducedBy,
            'penalty_percent'   => $penaltyPercent,
            'opens_at'          => $opensAt,
            'starts_at'         => $startsAt,
            'closes_at'         => $closesAt,
        ];
    }

    public function processVenueQrScan(User $user, Event $event): array
    {
        $gate = $this->assertWithinCheckInWindow($event);

        try {
            $result = DB::transaction(function () use ($user, $event, $gate): array {
                $lockedEvent = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();

                $registration = Registration::with(['waiver', 'checkIn', 'wellbeingResponses'])
                    ->where('user_id', $user->id)
                    ->where('event_id', $lockedEvent->id)
                    ->lockForUpdate()
                    ->first();

                $autoRegistered   = false;
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

                $pointsToAward = $gate['awarded_points'];
                $pointsAwarded = $this->awardPointsOnce($registration, 'attended_event', $pointsToAward);

                return [
                    'registration'       => $registration->fresh(['event', 'waiver', 'checkIn', 'wellbeingResponses']),
                    'auto_registered'    => $autoRegistered,
                    'already_checked_in' => $alreadyCheckedIn,
                    'points_earned'      => $pointsAwarded ? $pointsToAward : 0,
                    'is_late'            => $gate['is_late'],
                    'minutes_late'       => $gate['minutes_late'],
                    'base_points'        => $gate['base_points'],
                    'points_reduced_by'  => $gate['points_reduced_by'],
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
                'is_late'            => $gate['is_late'],
                'minutes_late'       => $gate['minutes_late'],
                'base_points'        => $gate['base_points'],
                'points_reduced_by'  => $gate['points_reduced_by'],
            ];
        }

        $this->invalidateAnalyticsCache($event);

        Log::info('WellLoop venue QR check-in processed', [
            'user_id'           => $user->id,
            'staff_id'          => $user->staff_id,
            'event_id'          => $event->id,
            'registration_id'   => $result['registration']->id,
            'is_late'           => $result['is_late'],
            'points_earned'     => $result['points_earned'],
            'points_reduced_by' => $result['points_reduced_by'],
        ]);

        $registration = $result['registration'];
        $hasPreWho5   = $registration->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isNotEmpty();

        return [
            ...$result,
            'needs_onboarding' => ! $registration->waiver || ! $hasPreWho5,
        ];
    }

    public function confirmRegistrationCheckIn(Registration $registration, string $method = 'qr_scan', ?User $actor = null): array
    {
        $registration->load(['event', 'user', 'waiver', 'checkIn']);
        $event    = $registration->event;
        $attendee = $registration->user;

        $isHrOverride = $actor && $actor->isHrCoordinator() && $method === 'manual';

        if (! $isHrOverride) {
            $gate   = $this->assertWithinCheckInWindow($event);
            $points = $gate['awarded_points'];
        } else {
            $points = (int) ($event->points_checkin ?? config('wellloop.checkin.default_points', 25));
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

    public function assertWithinCheckInWindow(Event $event): array
    {
        $gate = $this->evaluateGateStatus($event);

        if ($gate['status'] === 'locked') {
            throw new RuntimeException(
                'Check-in for ' . $event->name . ' opens 1 hour before the session on ' . $gate['opens_at']->format('D, d M Y \a\t H:i') . ' EAT.'
            );
        }

        if ($gate['status'] === 'closed') {
            throw new RuntimeException(
                'The check-in gate for ' . $event->name . ' closed at ' . $gate['closes_at']->format('H:i') . ' EAT (10 minutes after start time).'
            );
        }

        return $gate;
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