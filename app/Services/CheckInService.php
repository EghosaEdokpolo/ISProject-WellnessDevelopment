<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CheckIn;
use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckInService
{
    /**
     * Evaluate the live check-in gate window in Africa/Nairobi (EAT):
     * - 60 mins before start up to start time: OPEN (100% full points)
     * - 1 to 15 mins after start time: LATE GRACE (50% half points)
     * - 16+ mins after start time: CLOSED (0 points)
     */
    public function evaluateGateStatus(Event $event): array
    {
        $tz                = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $openBeforeMinutes = (int) config('wellloop.checkin.open_before_minutes', 60);
        $lateGraceMinutes  = (int) config('wellloop.checkin.late_grace_minutes', 15);
        $penaltyPercent    = (int) config('wellloop.checkin.late_penalty_percent', 50);

        $now        = now($tz);
        $startsAt   = $event->starts_at ? $event->starts_at->copy()->shiftTimezone($tz) : $now->copy();
        $opensAt    = $startsAt->copy()->subMinutes($openBeforeMinutes);
        $closesAt   = $startsAt->copy()->addMinutes($lateGraceMinutes);
        $cutoffAt   = $closesAt->copy()->endOfMinute(); // e.g., 10:15:59 still counts; 10:16:00 is closed
        $basePoints = (int) ($event->points_checkin ?? 20);

        if ($now->lt($opensAt)) {
            return [
                'status'            => 'locked',
                'is_open'           => false,
                'starts_at'         => $startsAt,
                'opens_at'          => $opensAt,
                'closes_at'         => $closesAt,
                'base_points'       => $basePoints,
                'awarded_points'    => $basePoints,
                'points_reduced_by' => 0,
                'penalty_percent'   => $penaltyPercent,
                'minutes_late'      => 0,
            ];
        }

        if ($now->lte($startsAt)) {
            return [
                'status'            => 'open',
                'is_open'           => true,
                'starts_at'         => $startsAt,
                'opens_at'          => $opensAt,
                'closes_at'         => $closesAt,
                'base_points'       => $basePoints,
                'awarded_points'    => $basePoints,
                'points_reduced_by' => 0,
                'penalty_percent'   => $penaltyPercent,
                'minutes_late'      => 0,
            ];
        }

        if ($now->lte($cutoffAt)) {
            $minutesLate     = min($lateGraceMinutes, max(1, (int) ceil($startsAt->diffInSeconds($now) / 60)));
            $halfPoints      = $basePoints > 0 ? max(1, (int) round($basePoints * ((100 - $penaltyPercent) / 100))) : 0;
            $pointsReducedBy = max(0, $basePoints - $halfPoints);

            return [
                'status'            => 'late_grace',
                'is_open'           => true,
                'starts_at'         => $startsAt,
                'opens_at'          => $opensAt,
                'closes_at'         => $closesAt,
                'base_points'       => $basePoints,
                'awarded_points'    => $halfPoints,
                'points_reduced_by' => $pointsReducedBy,
                'penalty_percent'   => $penaltyPercent,
                'minutes_late'      => $minutesLate,
            ];
        }

        return [
            'status'            => 'closed',
            'is_open'           => false,
            'starts_at'         => $startsAt,
            'opens_at'          => $opensAt,
            'closes_at'         => $closesAt,
            'base_points'       => $basePoints,
            'awarded_points'    => 0,
            'points_reduced_by' => $basePoints,
            'penalty_percent'   => 100,
            'minutes_late'      => (int) ceil($startsAt->diffInSeconds($now) / 60),
        ];
    }

    /**
     * Process a venue QR scan for either an authenticated or Staff-ID verified user.
     */
    public function processVenueQrScan(User $user, Event $event): array
    {
        $gate = $this->evaluateGateStatus($event);

        $existingReg = Registration::with(['checkIn', 'waiver', 'wellbeingResponses'])
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existingReg && ($existingReg->checkIn !== null || in_array($existingReg->status, ['checked_in', 'completed'], true))) {
            $needsOnboarding = ! $existingReg->waiver || $existingReg->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isEmpty();

            return [
                'registration'       => $existingReg,
                'auto_registered'    => false,
                'already_checked_in' => true,
                'points_earned'      => 0,
                'is_late'            => false,
                'minutes_late'       => 0,
                'base_points'        => $gate['base_points'],
                'points_reduced_by'  => 0,
                'needs_onboarding'   => $needsOnboarding,
            ];
        }

        if ($gate['status'] === 'locked') {
            throw new RuntimeException(
                'Check-in has not opened yet. The gate opens at ' . $gate['opens_at']->format('h:i A') . ' EAT (1 hour before start).'
            );
        }

        if ($gate['status'] === 'closed') {
            throw new RuntimeException(
                'Check-in closed at ' . $gate['closes_at']->format('h:i A') . ' EAT. Arrivals more than 15 minutes after start receive 0 points.'
            );
        }

        $autoRegistered = false;
        $pointsToAward  = (int) $gate['awarded_points'];
        $tz             = (string) config('wellloop.timezone', 'Africa/Nairobi');

        $registration = DB::transaction(function () use ($user, $event, $existingReg, $pointsToAward, $tz, &$autoRegistered): Registration {
            if (! $existingReg) {
                $autoRegistered = true;
                $reg = Registration::create([
                    'user_id'       => $user->id,
                    'event_id'      => $event->id,
                    'status'        => 'checked_in',
                    'consent_given' => false,
                ]);
            } else {
                $reg = $existingReg;
                if ($reg->status !== 'completed') {
                    $reg->update(['status' => 'checked_in']);
                }
            }

            CheckIn::firstOrCreate(
                ['registration_id' => $reg->id],
                [
                    'checked_in_at' => now($tz),
                    'method'        => 'qr_scan',
                ]
            );

            $this->awardCheckInPointsOnce($reg, $user, $pointsToAward);

            return $reg->fresh(['checkIn', 'waiver', 'wellbeingResponses', 'event']);
        });

        $this->clearAnalyticsCaches($event);

        $needsOnboarding = ! $registration->waiver
            || $registration->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->isEmpty();

        return [
            'registration'       => $registration,
            'auto_registered'    => $autoRegistered,
            'already_checked_in' => false,
            'points_earned'      => $pointsToAward,
            'is_late'            => $gate['status'] === 'late_grace',
            'minutes_late'       => $gate['minutes_late'],
            'base_points'        => $gate['base_points'],
            'points_reduced_by'  => $gate['points_reduced_by'],
            'needs_onboarding'   => $needsOnboarding,
        ];
    }

    /**
     * Confirm check-in via manual HR override or direct registration check-in.
     */
    public function confirmRegistrationCheckIn(Registration $registration, string $method, User $actor): string
    {
        $registration->load(['event', 'user', 'checkIn', 'waiver']);
        $event = $registration->event;
        $user  = $registration->user;

        if ($registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true)) {
            return "{$user->name} ({$user->staff_id}) is already checked in for {$event->name}.";
        }

        $gate = $this->evaluateGateStatus($event);
        $tz   = (string) config('wellloop.timezone', 'Africa/Nairobi');

        if (! $actor->isHrCoordinator() && ! $gate['is_open']) {
            throw new RuntimeException(
                $gate['status'] === 'locked'
                    ? 'Check-in gate opens at ' . $gate['opens_at']->format('h:i A') . ' EAT.'
                    : 'Check-in gate closed at ' . $gate['closes_at']->format('h:i A') . ' EAT (0 points after 15 mins).'
            );
        }

        // Full points if on time, half points if 1–15 mins late, 0 points if 16+ mins late
        $pointsToAward = $gate['is_open'] ? (int) $gate['awarded_points'] : 0;

        DB::transaction(function () use ($registration, $user, $method, $pointsToAward, $tz): void {
            CheckIn::firstOrCreate(
                ['registration_id' => $registration->id],
                [
                    'checked_in_at' => now($tz),
                    'method'        => $method,
                ]
            );

            if ($registration->status !== 'completed') {
                $registration->update(['status' => 'checked_in']);
            }

            if ($pointsToAward > 0) {
                $this->awardCheckInPointsOnce($registration, $user, $pointsToAward);
            }
        });

        $this->clearAnalyticsCaches($event);

        $ptsLabel = $pointsToAward > 0 ? "+{$pointsToAward} pts awarded" : "0 pts (after 15-min cutoff)";

        return "Checked in {$user->name} ({$user->staff_id}) — {$ptsLabel}.";
    }

    private function awardCheckInPointsOnce(Registration $registration, User $user, int $points): void
    {
        if ($points <= 0) {
            return;
        }

        $alreadyAwarded = PointsTransaction::where('registration_id', $registration->id)
            ->where('reason', 'attended_event')
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        PointsTransaction::create([
            'user_id'         => $user->id,
            'registration_id' => $registration->id,
            'reason'          => 'attended_event',
            'points'          => $points,
        ]);

        $user->increment('points_balance', $points);
    }

    private function clearAnalyticsCaches(Event $event): void
    {
        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));
        $prefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($prefix);
        if ($event->category) {
            Cache::forget($prefix . $event->category);
        }
    }
}