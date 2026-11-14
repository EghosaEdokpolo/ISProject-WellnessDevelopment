<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepartmentalEngagementService
{
    public function ranking(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= RetentionService::academicYearStart();
        $end   ??= RetentionService::academicYearEnd();

        // 1. Get total staff per department
        $staffCounts = User::where('role', 'staff')
            ->whereNotNull('department')
            ->select('department', DB::raw('count(*) as total_staff'))
            ->groupBy('department')
            ->get()
            ->keyBy('department');

        // 2. Get unique attendees per department (Only completed events)
        $attendees = Registration::where('status', 'completed')
            ->whereBetween('registrations.created_at', [$start, $end])
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->select('users.department', DB::raw('count(distinct users.id) as unique_attendees'))
            ->groupBy('users.department')
            ->get()
            ->keyBy('department');

        $rows = collect();
        $totalOrgRate = 0;
        $engagedCount = 0;
        $disengagedCount = 0;

        foreach ($staffCounts as $dept => $data) {
            $totalStaff = $data->total_staff;
            $uniqueAttendees = isset($attendees[$dept]) ? $attendees[$dept]->unique_attendees : 0;
            
            $rate = $totalStaff > 0 ? round(($uniqueAttendees / $totalStaff) * 100, 1) : 0;
            $status = $rate >= 60 ? 'engaged' : ($rate >= 30 ? 'developing' : 'disengaged');

            if ($status === 'engaged') $engagedCount++;
            if ($status === 'disengaged') $disengagedCount++;
            $totalOrgRate += $rate;

            $rows->push([
                'department'       => $dept,
                'code'             => $this->shortCode($dept),
                'total_staff'      => $totalStaff,
                'unique_attendees' => $uniqueAttendees,
                'engagement_rate'  => $rate,
                'status'           => $status,
            ]);
        }

        $totalDepartments = $rows->count();
        $orgAverage = $totalDepartments > 0 ? round($totalOrgRate / $totalDepartments, 1) : 0;

        $sortedRows = $rows->sortByDesc('engagement_rate')->values();
        
        // SENIOR FIX: Ensure Top 5 and Bottom 5 don't overlap if there are < 10 departments
        $halfCount = ceil($totalDepartments / 2);
        $takeCount = min(5, $halfCount);

        return [
            'stats' => [
                'total_departments' => $totalDepartments,
                'org_average'       => $orgAverage,
                'engaged_count'     => $engagedCount,
                'disengaged_count'  => $disengagedCount,
            ],
            'most_active'     => $sortedRows->take($takeCount)->toArray(),
            'needs_attention' => $sortedRows->reverse()->take($takeCount)->toArray(),
            'all_departments' => $sortedRows->toArray(),
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