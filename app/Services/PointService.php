<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointService
{
    public function award(Registration $registration, string $reason, int $points): void
    {
        $exists = PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists();
        if ($exists) return;

        DB::transaction(function () use ($registration, $reason, $points) {
            PointsTransaction::create([
                'user_id'         => $registration->user_id,
                'registration_id' => $registration->id,
                'reason'          => $reason,
                'points'          => $points,
            ]);
            $registration->user->increment('points_balance', $points);
        });
    }

    public function resetAll(): int
    {
        $count = 0;
        $academicYear = date('Y') . '/' . (date('Y') + 1);

        // Wrap in a transaction to prevent race conditions
        DB::transaction(function () use (&$count, $academicYear) {
            User::where('role', 'staff')
                ->where('points_balance', '>', 0)
                ->chunkById(100, function ($users) use (&$count, $academicYear) {
                    foreach ($users as $user) {
                        PointsTransaction::create([
                            'user_id'       => $user->id,
                            'reason'        => 'annual_reset',
                            'points'        => -$user->points_balance,
                            'academic_year' => $academicYear,
                        ]);
                        
                        // Direct assignment bypasses mass-assignment and forces the DB update
                        $user->points_balance = 0;
                        $user->save();
                        
                        $count++;
                    }
                });
        });

        return $count;
    }
}