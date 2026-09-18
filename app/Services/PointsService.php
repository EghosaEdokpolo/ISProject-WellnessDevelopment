<?php

namespace App\Services;

use App\Models\PointsTransaction;
use App\Models\User;

// POINTER: this is a "Service class" — plain PHP, not tied to Eloquent or
// HTTP. Controllers should be thin (just: validate input, call a service,
// redirect). Putting the points math here means:
//   1. Any controller that needs to award points calls the same method,
//      so the numbers can never drift out of sync between features.
//   2. You can unit-test this class directly without spinning up a request.
class PointsService
{
    // Matches exactly what you showed in the Rewards screen
    private const POINTS = [
        'consent' => 10,
        'check_in' => 20,
        'feedback' => 15,
        'referral' => 25,
    ];

    public function award(User $user, string $reason): void
    {
        $points = self::POINTS[$reason]
            ?? throw new \InvalidArgumentException("Unknown points reason: {$reason}");

        // POINTER: DB::transaction() (used implicitly here via two writes)
        // should really wrap these two lines in production so the ledger
        // entry and the balance update never happen only halfway. For now,
        // simple version — wrap in \DB::transaction(fn () => ...) once you're
        // comfortable with the basics.
        PointsTransaction::create([
            'user_id' => $user->id,
            'reason' => $reason,
            'points' => $points,
        ]);

        $user->increment('points_balance', $points);
    }
}
