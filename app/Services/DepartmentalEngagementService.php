<?php

namespace App\Services;

use App\Models\Department;
use App\Models\EventAttendance;
use Carbon\Carbon;

class DepartmentalEngagementService
{
    public function ranking(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= now()->startOfYear();
        $end   ??= now()->endOfYear();

        $data = Department::withCount(['staff as total_staff' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(function (Department $dept) use ($start, $end) {
                $uniqueAttendees = EventAttendance::whereHas('staff', fn ($q) => $q->where('department_id', $dept->id))
                    ->where('attended', true)
                    ->whereBetween('checked_in_at', [$start, $end])
                    ->distinct('staff_id')
                    ->count('staff_id');

                $rate = $dept->total_staff > 0
                    ? round(($uniqueAttendees / $dept->total_staff) * 100, 1)
                    : 0;

                return [
                    'id'               => $dept->id,
                    'department'       => $dept->name,
                    'code'             => $dept->code,
                    'total_staff'      => $dept->total_staff,
                    'unique_attendees' => $uniqueAttendees,
                    'engagement_rate'  => $rate,
                    'status'           => $rate >= 60 ? 'engaged' : ($rate >= 30 ? 'developing' : 'disengaged'),
                ];
            })
            ->sortByDesc('engagement_rate')
            ->values();

        return [
            'most_active'     => $data->take(5)->values()->toArray(),
            'needs_attention' => $data->reverse()->take(5)->values()->toArray(),
            'all_departments' => $data->toArray(),
            'period'          => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        ];
    }
}