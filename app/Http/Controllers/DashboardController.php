<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Event;
use App\Models\User;
use App\Models\DataProcessingLog;
use App\Services\DepartmentalEngagementService;
use App\Services\HrDashboardService;
use App\Services\RetentionService;
use App\Services\WellnessImpactService;
use App\Services\PointService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly HrDashboardService $dashboardService
    ) {}

    public function hrOverview(): View
    {
        return view('hrview.hrdb_overview', $this->dashboardService->getOverviewData());
    }

    public function attendees(Request $request): View 
    {
        $registrations = $this->dashboardService->getAttendees($request); 
        $events = Event::orderByDesc('starts_at')->get(); 
        return view('hrview.hrdb_attendeetable', compact('registrations', 'events'));
    }

    public function rewards(): View
    {
        $me = auth()->user();
        $leaderboard = User::where('role', 'staff')->where('department', $me->department)->orderByDesc('points_balance')->limit(10)->get();
        return view('dashboard.rewards', compact('me', 'leaderboard'));
    }

    public function eventmanagement(): View
    {
        $events = Event::with('createdBy')->orderByDesc('starts_at')->get();
        return view('hrview.hrdb_eventsmanagement', compact('events'));
    }

    public function impactAnalytics(WellnessImpactService $service): View
    {
        $data = Cache::remember('hr_impact_data_v6_' . request('category'), 300, function () use ($service) {
            $category = request('category');
            $categoriesData = $service->categoryBreakdown($category);
            
            $highestDim = null; 
            $lowestDim = null;
            if ($category === null && count($categoriesData) > 0) {
                $sorted = collect($categoriesData)->sortByDesc('avg_lift');
                $highestDim = $sorted->keys()->first();
                $lowestDim = $sorted->keys()->last();
            }

            return [
                'categories' => $categoriesData,
                'eventTable' => $service->eventTable($category),
                'activeCategory' => $category,
                'highestDim' => $highestDim,
                'lowestDim' => $lowestDim,
            ];
        });

        return view('hrview.hrdb_impact', $data);
    }

    public function retentionMetrics(RetentionService $service): View
    {
        return view('hrview.hrdb_retention', ['retention' => $service->sustainedParticipation()]);
    }

    public function departmentAnalysis(DepartmentalEngagementService $service): View
    {
        return view('hrview.hrdb_departmental', ['ranking' => $service->ranking()]);
    }

    public function settings(): View
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        $cycleStart = $month >= 9 ? $year : $year - 1;
        $cycleEnd   = $cycleStart + 1;
        
        return view('hrview.hrdb_settings', [
            'user' => auth()->user(),
            'cycleStart' => $cycleStart,
            'cycleEnd' => $cycleEnd
        ]);
    }

    public function updateSettings(UpdateSettingsRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $user->name = $request->validated('name');
        
        if ($request->filled('password')) { 
            $user->password = bcrypt($request->validated('password')); 
        }
        
        $user->save();
        
        Cache::forget('hr_overview_data_v8');
        Cache::forget('hr_impact_data_v6_');

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function resetPoints(PointService $pointService): RedirectResponse
    {
        $count = $pointService->resetAll();
        
        DataProcessingLog::record('points_reset', [
            'users_affected' => $count,
            'academic_year'  => date('Y') . '/' . (date('Y') + 1),
        ]);

        return redirect()->back()->with('success', "Successfully reset points for {$count} staff members.");
    }
}