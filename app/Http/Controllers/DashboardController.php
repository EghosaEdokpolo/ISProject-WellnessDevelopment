<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\WellbeingResponse;
use App\Services\DepartmentalEngagementService;
use App\Services\RetentionService;
use App\Services\WellnessImpactService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function hrOverview(): View
    {
        $totalStaff      = User::where('role', 'staff')->count();
        $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();

        $sustainedStaffCount = User::where('role', 'staff')
            ->whereHas('registrations', fn ($q) => $q->where('status', 'completed'), '>=', 2)
            ->count();

        $start = RetentionService::academicYearStart();
        $end   = RetentionService::academicYearEnd();

        $eventsThisAcademicYear = Event::whereBetween('created_at', [$start, $end])->count();

        $avgLift = round(
            WellbeingResponse::where('stage', 'post')->avg('total_score')
            - WellbeingResponse::where('stage', 'pre')->avg('total_score'),
            1
        );

        $totalPointsEarned = User::where('role', 'staff')->sum('points_balance') ?? 0;

        $physicalCount = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'physical'))->count();
        $mentalCount = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'mental'))->count();
        $financialCount = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'financial'))->count();
        $socialCount = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'social'))->count();

        $recentActivities = Registration::with(['user', 'event'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($reg) {
                if ($reg->status === 'completed') {
                    return ['text' => "<strong>{$reg->user->name}</strong> submitted wellness feedback", 'time' => $reg->updated_at->diffForHumans(), 'marker' => 'marker-green'];
                }
                if ($reg->status === 'checked_in') {
                    return ['text' => "<strong>{$reg->user->name}</strong> checked in via QR code", 'time' => $reg->updated_at->diffForHumans(), 'marker' => 'marker-blue'];
                }
                return ['text' => "<strong>{$reg->user->name}</strong> registered for <strong>{$reg->event->name}</strong>", 'time' => $reg->updated_at->diffForHumans(), 'marker' => 'marker-gold'];
            });

        return view('hrview.hrdb_overview', [
            'registrationRate' => $totalStaff ? round($registeredStaff / $totalStaff * 100, 1) : 0,
            'sustainedParticipationRate' => $totalStaff ? round($sustainedStaffCount / $totalStaff * 100, 1) : 0,
            'avgLift' => $avgLift ?: 0,
            'registeredStaff' => $registeredStaff,
            'totalStaff' => $totalStaff,
            'totalPointsEarned' => $totalPointsEarned,
            'physicalCount' => $physicalCount,
            'mentalCount' => $mentalCount,
            'financialCount' => $financialCount,
            'socialCount' => $socialCount,
            'eventsThisSemester' => $eventsThisAcademicYear,
            'recentActivities' => $recentActivities,
        ]);
    }

    public function rewards()
    {
        $me = auth()->user();
        $leaderboard = User::where('role', 'staff')->where('department', $me->department)->orderByDesc('points_balance')->limit(10)->get();
        return view('dashboard.rewards', compact('me', 'leaderboard'));
    }

    public function attendees()
    {
        $registrations = Registration::with(['user', 'event', 'waiver', 'checkIn', 'feedback', 'wellbeingResponses'])->latest()->get();
        return view('hrview.hrdb_attendeetable', compact('registrations'));
    }

    public function eventmanagement()
    {
        $events = Event::with('createdBy')->orderByDesc('starts_at')->get();
        return view('hrview.hrdb_eventsmanagement', compact('events'));
    }

    public function impactAnalytics(WellnessImpactService $service): View
    {
        $category = request('category');
        return view('hrview.hrdb_impact', [
            'categories' => $service->categoryBreakdown($category),
            'eventTable' => $service->eventTable($category),
            'activeCategory' => $category,
        ]);
    }

    public function retentionMetrics(RetentionService $service): View
    {
        return view('hrview.hrdb_retention', ['retention' => $service->sustainedParticipation()]);
    }

    public function departmentAnalysis(DepartmentalEngagementService $service): View
    {
        return view('hrview.hrdb_departmental', ['ranking' => $service->ranking()]);
    }

    public function settings()
    {
        return view('hrview.hrdb_settings', ['user' => auth()->user()]);
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $request->validate(['name' => 'required|string|max:255', 'password' => 'nullable|string|min:8|confirmed']);
        $user->name = $request->name;
        if ($request->filled('password')) { $user->password = bcrypt($request->password); }
        $user->save();
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}