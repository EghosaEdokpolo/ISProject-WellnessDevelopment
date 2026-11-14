<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class DepartmentalEngagementService
{
    public function ranking(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= RetentionService::academicYearStart(); 
        $end ??= RetentionService::academicYearEnd();
        
        $departments = User::where('role', 'staff')->whereNotNull('department')->distinct()->pluck('department');

        $rows = $departments->map(function ($dept) use ($start, $end) {
            $totalStaff = User::where('role', 'staff')->where('department', $dept)->count();
            $uniqueAttendees = User::where('role', 'staff')->where('department', $dept)->whereHas('registrations', function ($q) use ($start, $end) {
                $q->where('status', 'completed')->whereBetween('created_at', [$start, $end]);
            })->count();

            $rate = $totalStaff > 0 ? round(($uniqueAttendees / $totalStaff) * 100, 1) : 0;
            return [
                'department' => $dept, 
                'code' => $this->shortCode($dept), 
                'total_staff' => $totalStaff,
                'unique_attendees' => $uniqueAttendees, 
                'engagement_rate' => $rate,
                'status' => $rate >= 60 ? 'engaged' : ($rate >= 30 ? 'developing' : 'disengaged'),
            ];
        })->sortByDesc('engagement_rate')->values();

        // 1. Calculate overall organization average at the current Academic Year End
        $avgEnd = $rows->count() > 0 ? round($rows->avg('engagement_rate'), 0) : 0;

        // 2. YEAR START CARRY-FORWARD: Compute the EXACT engagement rate of the prior year
        // Calculate a historical time window exactly 1 year before the current cycle starts
        $pastStart = (clone $start)->subYear();
        $pastEnd = (clone $start)->subSecond();

        // Count staff entries from that historical window to determine if data exists
        $pastAttendees = User::where('role', 'staff')->whereHas('registrations', function ($q) use ($pastStart, $pastEnd) {
            $q->where('status', 'completed')->whereBetween('created_at', [$pastStart, $pastEnd]);
        })->count();

        $totalStaffHistoric = User::where('role', 'staff')->whereNotNull('department')->count();

        // If historical logs are found, calculate the exact historic rate to use as the starting baseline.
        // If the database is completely empty (starting fresh), it defaults cleanly to 0.
        if ($pastAttendees > 0 && $totalStaffHistoric > 0) {
            $avgStart = round(($pastAttendees / $totalStaffHistoric) * 100, 0);
        } else {
            $avgStart = 0; // Fresh system initialization baseline
        }

        // 3. Compute the automatic Org-Wide Growth percentage points gap
        $growthPoints = $avgEnd - $avgStart;
        $growthString = ($growthPoints >= 0 ? '+' : '') . $growthPoints . 'pp';
        return [
            'most_active'     => $rows->take(5)->values()->toArray(), 
            'needs_attention' => $rows->sortBy('engagement_rate')->take(5)->values()->toArray(), 
            'all_departments' => $rows->toArray(), 
            'period'          => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'org_start'       => $avgStart . '%',
            'org_end'         => $avgEnd . '%',
            'org_growth'      => $growthString
        ];
    }

    protected function shortCode(string $department): string
    {
        return match (true) {
            str_contains($department, 'Computing') => 'SCES', 
            str_contains($department, 'Law') => 'SLS',
            str_contains($department, 'Business') => 'SBS', 
            str_contains($department, 'Humanities') => 'SHSS',
            str_contains($department, 'Finance') => 'FIN', 
            str_contains($department, 'Human Res') => 'HR',
            str_contains($department, 'Cafeteria') => 'CAF', 
            str_contains($department, 'Housekeep') => 'HK',
            default => strtoupper(substr($department, 0, 3)),
        };
    }
}
