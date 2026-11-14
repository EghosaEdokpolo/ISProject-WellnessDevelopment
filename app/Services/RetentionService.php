<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class RetentionService
{
    public function sustainedParticipation(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= self::academicYearStart();
        $end   ??= self::academicYearEnd();

        // Fetch all active staff and count their completed registrations
        $staff = User::where('role', 'staff')
            ->withCount(['registrations as attended_count' => function ($query) use ($start, $end) {
                $query->where('status', 'completed')->whereBetween('created_at', [$start, $end]);
            }])
            ->get();

        $totalActive = $staff->count();
        
        // Calculate Sustained (2+ events)
        $sustained = $staff->where('attended_count', '>=', 2)->count();
        
        // Calculate Loyalty Distribution for the chart
        $distribution = [
            '0 Events' => $staff->where('attended_count', 0)->count(),
            '1 Event'  => $staff->where('attended_count', 1)->count(),
            '2 Events' => $staff->where('attended_count', 2)->count(),
            '3+ Events'=> $staff->where('attended_count', '>=', 3)->count(),
        ];

        // Calculate Avg Loyalty
        $totalAttended = $staff->sum('attended_count');
        $avgLoyalty = $totalActive > 0 ? round($totalAttended / $totalActive, 1) : 0;

        // Get Top Attendees
        $topAttendees = $staff->sortByDesc('attended_count')->take(5)->values();

        return [
            'total_active_staff'      => $totalActive,
            'sustained_attendees'     => $sustained,
            'sustained_participation' => $totalActive > 0 ? round(($sustained / $totalActive) * 100, 1) : 0,
            'avg_loyalty'             => $avgLoyalty,
            'distribution'            => $distribution,
            'top_attendees'           => $topAttendees,
            'period'                  => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        ];
    }

    public static function academicYearStart(): Carbon
    {
        $month = (int) now()->format('n');
        $year  = (int) now()->format('Y');

        return $month >= 9
            ? Carbon::create($year, 9, 1)->startOfDay()
            : Carbon::create($year - 1, 9, 1)->startOfDay();
    }

    public static function academicYearEnd(): Carbon
    {
        return self::academicYearStart()->copy()->addYear()->subDay()->endOfDay();
    }
}