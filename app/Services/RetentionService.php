<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use Carbon\Carbon;

class RetentionService
{
    public function sustainedParticipation(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= self::academicYearStart(); 
        $end ??= self::academicYearEnd();
        
        $totalActive = User::where('role', 'staff')->count();

        $sustained = User::where('role', 'staff')->whereHas('registrations', function ($q) use ($start, $end) {
            $q->where('status', 'completed')->whereBetween('created_at', [$start, $end]);
        }, '>=', 2)->count();

        // Count exactly how many events exist in the database for this academic year window
        $totalEventsCount = Event::whereBetween('starts_at', [$start, $end])->count();
        $totalEventsCount = $totalEventsCount > 0 ? $totalEventsCount : 1; // Prevent division by zero if empty

        // 1. CALCULATE HEATMAP MATRIX DATA ROWS
        $departments = User::where('role', 'staff')->whereNotNull('department')->distinct()->pluck('department');
        $heatmapData = [];

        foreach ($departments as $dept) {
            $totalStaff = User::where('role', 'staff')->where('department', $dept)->count();
            if ($totalStaff === 0) continue;

            $days = $start->diffInDays($end) / 3;
            $q1End = (clone $start)->addDays($days);
            $q2End = (clone $start)->addDays($days * 2);

            $q1Count = User::where('role', 'staff')->where('department', $dept)->whereHas('registrations', function($q) use ($start, $q1End) {
                $q->where('status', 'completed')->whereBetween('created_at', [$start, $q1End]);
            })->count();

            $q2Count = User::where('role', 'staff')->where('department', $dept)->whereHas('registrations', function($q) use ($q1End, $q2End) {
                $q->where('status', 'completed')->whereBetween('created_at', [$q1End, $q2End]);
            })->count();

            $q3Count = User::where('role', 'staff')->where('department', $dept)->whereHas('registrations', function($q) use ($q2End, $end) {
                $q->where('status', 'completed')->whereBetween('created_at', [$q2End, $end]);
            })->count();

            $heatmapData[] = [
                'department' => $dept,
                'q1' => round(($q1Count / $totalStaff) * 100, 0),
                'q2' => round(($q2Count / $totalStaff) * 100, 0),
                'q3' => round(($q3Count / $totalStaff) * 100, 0),
            ];
        }

        // 2. OPTIMIZED PERFORMANCE SELECTION LAYER
        // We select only the columns we need to dramatically reduce server response latency times!
        $staffMembers = User::where('role', 'staff')
            ->select('id', 'name', 'department')
            ->withCount(['registrations' => function($q) use ($start, $end) {
                $q->where('status', 'completed')->whereBetween('created_at', [$start, $end]);
            }])->get();

        $frequency = ['0 events' => 0, '1 event' => 0, '2-3 events' => 0, '4-5 events' => 0, 'All Events' => 0];

        foreach ($staffMembers as $member) {
            $count = $member->registrations_count;
            if ($count === 0) $frequency['0 events']++;
            elseif ($count === 1) $frequency['1 event']++;
            elseif ($count <= 3) $frequency['2-3 events']++;
            elseif ($count <= 5) $frequency['4-5 events']++;
            else $frequency['All Events']++;
        }

        // 3. CALCULATE GRANULAR STAFF LOYALTY PROFILE MATRIX (Using dynamic event denominator)
        $individualScores = $staffMembers->map(function($member) use ($totalEventsCount) {
            $count = $member->registrations_count;
            return [
                'name' => $member->name,
                'department' => $member->department ?? 'Unassigned',
                'attended_count' => $count,
                // Calculates out of totalEventsCount instead of hardcoded 6!
                'loyalty_rate' => round(($count / $totalEventsCount) * 100, 0) > 100 ? 100 : round(($count / $totalEventsCount) * 100, 0),
            ];
        })->sortByDesc('attended_count')->take(20)->values()->toArray();

        return [
            'total_active_staff'      => $totalActive,
            'sustained_attendees'     => $sustained,
            'sustained_participation' => $totalActive > 0 ? round(($sustained / $totalActive) * 100, 1) : 0,
            'period'                  => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'heatmap_data'            => $heatmapData,
            'loyalty_frequency'       => $frequency,
            'individual_scores'       => $individualScores,
            'total_events_count'      => $totalEventsCount // Sent to Blade view parameters
        ];
    }

    public static function academicYearStart(): Carbon
    {
        $month = (int) now()->format('n'); 
        $year = (int) now()->format('Y');
        return $month >= 9 ? Carbon::create($year, 9, 1)->startOfDay() : Carbon::create($year - 1, 9, 1)->startOfDay();
    }

    public static function academicYearEnd(): Carbon
    {
        return self::academicYearStart()->copy()->addYear()->subDay()->endOfDay();
    }
}
