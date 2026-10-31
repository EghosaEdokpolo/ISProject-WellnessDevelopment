<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\WellbeingResponse;
use Illuminate\View\View;

 /**
     * HR Coordinator "Overview" dashboard.
     * Calculates the real-time system metrics displayed on your layout cards.
     */
class HrOverviewController extends Controller
{
    /**
     * Display the core HR overview metric insights.
     */
    public function index(): View
    {
        // 1. Core staff headcount metrics
        $totalStaff = User::where('role', 'staff')->count();
        $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();

        // 2. Multi-event retention calculation
        $sustainedStaffCount = User::where('role', 'staff')
            ->whereHas('registrations', function ($query) {
                $query->where('status', 'completed');
            }, '>=', 2)
            ->count();

        // 3. Academic Year window calculation
        $currentMonth = date('n');
        $currentYear = date('Y');
        if ($currentMonth >= 9) {
            $academicYearStart = "{$currentYear}-09-01 00:00:00";
            $academicYearEnd   = ($currentYear + 1) . "-08-31 23:59:59";
        } else {
            $academicYearStart = ($currentYear - 1) . "-09-01 00:00:00";
            $academicYearEnd   = "{$currentYear}-08-31 23:59:59";
        }

        $eventsThisAcademicYear = Event::whereBetween('created_at', [$academicYearStart, $academicYearEnd])->count();
    
        // 4. Wellbeing survey processing
        $avgLift = round(
            WellbeingResponse::where('stage', 'post')->avg('total_score')
            - WellbeingResponse::where('stage', 'pre')->avg('total_score'),
            1
        );

        $totalPointsEarned = User::where('role', 'staff')->sum('points_balance') ?? 0;

        // 5. Category sign-up metric counts for our overview bar graph pillars
        $physicalCount = Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) { $query->where('category', 'physical'); })->count();

        $mentalCount = Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) { $query->where('category', 'mental'); })->count();

        $financialCount = Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) { $query->where('category', 'financial'); })->count();

        $socialCount = Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) { $query->where('category', 'social'); })->count();

        // 6. Recent activity timeline data mapping
        $recentActivities = Registration::with(['user', 'event'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($reg) {
                if ($reg->feedback_submitted) {
                    $text = "<strong>{$reg->user->name}</strong> submitted wellness feedback";
                    $marker = "marker-green";
                } elseif ($reg->status === 'attended') {
                    $text = "<strong>{$reg->user->name}</strong> checked in via QR code";
                    $marker = "marker-blue";
                } else {
                    $text = "<strong>{$reg->user->name}</strong> registered for <strong>{$reg->event->name}</strong>";
                    $marker = "marker-gold";
                }
                
                return [
                    'text' => $text,
                    'time' => $reg->updated_at->diffForHumans(),
                    'marker' => $marker
                ];
            });

        // 7. Hand variables clean off to the view framework
        return view('hrview.hrdb_overview', [
            'registrationRate' => $totalStaff ? round($registeredStaff / $totalStaff * 100, 1) : 0,
            'sustainedParticipationRate' => $totalStaff ? round($sustainedStaffCount / $totalStaff * 100, 1) : 0, 
            'avgLift' => $avgLift ?: 0,
            'registeredStaff' => $registeredStaff,
            'totalStaff' => $totalStaff,
            'totalPointsEarned' => $totalPointsEarned,
            'physicalCount'    => $physicalCount,
            'mentalCount'      => $mentalCount,
            'financialCount'   => $financialCount,
            'socialCount'      => $socialCount,
            'eventsThisSemester' => $eventsThisAcademicYear,
            'recentActivities' => $recentActivities, 
        ]);
    }
}
