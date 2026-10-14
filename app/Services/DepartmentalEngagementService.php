<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class DepartmentalEngagementService
{
    public function ranking(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= RetentionService::academicYearStart();
        $end   ??= RetentionService::academicYearEnd();

        $departments = User::where('role', 'staff')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $rows = $departments->map(function ($dept) use ($start, $end) {
            $totalStaff = User::where('role', 'staff')
                ->where('department', $dept)
                ->count();

            $uniqueAttendees = User::where('role', 'staff')
                ->where('department', $dept)
                ->whereHas('registrations', function ($q) use ($start, $end) {
                    $q->where('status', 'completed')
                      ->whereBetween('created_at', [$start, $end]);
                })
                ->count();

            $rate = $totalStaff > 0 ? round(($uniqueAttendees / $totalStaff) * 100, 1) : 0;

            return [
                'department'       => $dept,
                'code'             => $this->shortCode($dept),
                'total_staff'      => $totalStaff,
                'unique_attendees' => $uniqueAttendees,
                'engagement_rate'  => $rate,
                'status'           => $rate >= 60 ? 'engaged' : ($rate >= 30 ? 'developing' : 'disengaged'),
            ];
        })->sortByDesc('engagement_rate')->values();

        return [
            'most_active'     => $rows->take(5)->values()->toArray(),
            'needs_attention' => $rows->reverse()->take(5)->values()->toArray(),
            'all_departments' => $rows->toArray(),
            'period'          => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        ];
    }

    protected function shortCode(string $department): string
    {
        return match (true) {
            str_contains($department, 'Computing')  => 'SCES',
            str_contains($department, 'Law')        => 'SOL',
            str_contains($department, 'Business')   => 'SBS',
            str_contains($department, 'Humanities') => 'SHSS',
            str_contains($department, 'Finance')    => 'FIN',
            str_contains($department, 'Human Res')  => 'HR',
            str_contains($department, 'Cafeteria')  => 'CAF',
            str_contains($department, 'Housekeep')  => 'HK',
            default => strtoupper(substr($department, 0, 3)),
        };
    }
}