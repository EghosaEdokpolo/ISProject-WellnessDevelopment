<?php

namespace App\Services;

use App\Models\EventAttendance;
use App\Models\Staff;
use Carbon\Carbon;

class RetentionService
{
    public function sustainedParticipation(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= now()->startOfYear();
        $end   ??= now()->endOfYear();

        $totalActive = Staff::where('is_active', true)->count();

        $sustained = EventAttendance::where('attended', true)
            ->whereBetween('checked_in_at', [$start, $end])
            ->select('staff_id')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        return [
            'total_active_staff'      => $totalActive,
            'sustained_attendees'     => $sustained,
            'sustained_participation' => $totalActive > 0
                ? round(($sustained / $totalActive) * 100, 1)
                : 0,
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        ];
    }
}