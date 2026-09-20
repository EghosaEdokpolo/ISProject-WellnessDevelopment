<?php

namespace App\Services;

use App\Models\PointsTransaction;
use App\Models\Staff;

class PointService
{
    public static function academicYear(): string
    {
        $y = (int) now()->format('Y');
        $m = (int) now()->format('n');
        return $m >= 9 ? "{$y}/" . ($y + 1) : ($y - 1) . "/{$y}";
    }

    public function award(Staff $staff, int $points, string $reason, ?int $eventId = null): PointsTransaction
    {
        $tx = PointsTransaction::create([
            'staff_id'      => $staff->id,
            'event_id'      => $eventId,
            'points'        => $points,
            'reason'        => $reason,
            'academic_year' => self::academicYear(),
        ]);

        $staff->increment('points_balance', $points);

        return $tx;
    }

    public function resetAll(): int
    {
        $year    = self::academicYear();
        $count   = 0;

        Staff::where('points_balance', '>', 0)->chunkById(100, function ($staff) use ($year, &$count) {
            foreach ($staff as $s) {
                PointsTransaction::create([
                    'staff_id'      => $s->id,
                    'points'        => -$s->points_balance,
                    'reason'        => 'annual_reset',
                    'academic_year' => $year,
                ]);
                $s->update(['points_balance' => 0]);
                $count++;
            }
        });

        return $count;
    }
}