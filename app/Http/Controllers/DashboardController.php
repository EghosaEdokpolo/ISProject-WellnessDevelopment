<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\CheckIn;
use App\Models\DataProcessingLog;
use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use App\Models\WellbeingResponse;
use App\Services\DepartmentalEngagementService;
use App\Services\HrDashboardService;
use App\Services\PointService;
use App\Services\RetentionService;
use App\Services\WellnessImpactService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $staffUsers = User::where('role', 'staff')->orderBy('name')->get();

        return view('hrview.hrdb_attendeetable', compact('registrations', 'events', 'staffUsers'));
    }

    public function logHistoricalAttendance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_id'  => ['required', 'exists:events,id'],
            'user_id'   => ['required', 'exists:users,id'],
            'who5_pre'  => ['nullable', 'integer', 'between:0,25'],
            'who5_post' => ['nullable', 'integer', 'between:0,25'],
            'award_pts' => ['nullable', 'boolean'],
        ]);

        $event = Event::findOrFail($data['event_id']);
        $user  = User::findOrFail($data['user_id']);

        $hasPost = isset($data['who5_post']) && $data['who5_post'] !== null;
        $status  = $hasPost ? 'completed' : 'checked_in';

        DB::transaction(function () use ($request, $data, $event, $user, $hasPost, $status): void {
            $registration = Registration::updateOrCreate(
                ['user_id' => $user->id, 'event_id' => $event->id],
                ['status' => $status, 'consent_given' => true]
            );

            CheckIn::firstOrCreate(
                ['registration_id' => $registration->id],
                ['checked_in_at' => $event->starts_at ?? now(), 'method' => 'hr_retroactive']
            );

            if (isset($data['who5_pre']) && $data['who5_pre'] !== null) {
                $preTotal = (int) $data['who5_pre'];
                $base = intdiv($preTotal, 5);
                $rem  = $preTotal % 5;

                WellbeingResponse::updateOrCreate(
                    ['registration_id' => $registration->id, 'stage' => 'pre'],
                    [
                        'item_cheerful'   => $base + ($rem > 0 ? 1 : 0),
                        'item_calm'       => $base + ($rem > 1 ? 1 : 0),
                        'item_active'     => $base + ($rem > 2 ? 1 : 0),
                        'item_rested'     => $base + ($rem > 3 ? 1 : 0),
                        'item_interested' => $base,
                        'total_score'     => $preTotal,
                    ]
                );
            }

            if ($hasPost) {
                $postTotal = (int) $data['who5_post'];
                $base = intdiv($postTotal, 5);
                $rem  = $postTotal % 5;

                WellbeingResponse::updateOrCreate(
                    ['registration_id' => $registration->id, 'stage' => 'post'],
                    [
                        'item_cheerful'   => $base + ($rem > 0 ? 1 : 0),
                        'item_calm'       => $base + ($rem > 1 ? 1 : 0),
                        'item_active'     => $base + ($rem > 2 ? 1 : 0),
                        'item_rested'     => $base + ($rem > 3 ? 1 : 0),
                        'item_interested' => $base,
                        'total_score'     => $postTotal,
                    ]
                );
            }

            if ($request->boolean('award_pts', true)) {
                $checkinPts = (int) ($event->points_checkin ?? 20);
                if (!PointsTransaction::where('registration_id', $registration->id)->where('reason', 'attended_event')->exists()) {
                    PointsTransaction::create([
                        'user_id'         => $user->id,
                        'registration_id' => $registration->id,
                        'reason'          => 'attended_event',
                        'points'          => $checkinPts,
                    ]);
                    $user->increment('points_balance', $checkinPts);
                }
            }
        });

        Cache::forget('hr_overview_data_v21');
        Cache::forget('hr_impact_data_v6_');
        Cache::forget('hr_impact_data_v6_' . $event->category);

        return redirect()->route('attendees.index')
            ->with('success', "Historical attendance logged for {$user->name} ({$event->name}).");
    }

    public function rewards(): View
    {
        $me = auth()->user();
        $leaderboard = User::where('role', 'staff')
            ->where('department', $me->department)
            ->orderByDesc('points_balance')
            ->limit(10)
            ->get();

        // Use cumulative earned points for Appraisal Tier standing so redeeming rewards doesn't demote staff
        $cumulativeEarned = (int) PointsTransaction::where('user_id', $me->id)
            ->where('points', '>', 0)
            ->sum('points');
        $tierPoints = max((int) $me->points_balance, $cumulativeEarned);

        if ($tierPoints >= 200) {
            $tier = 'Gold';
            $nextTier = 'Max Level';
            $progress = 100;
            $tierColor = 'text-amber-800 bg-amber-100 border-amber-300 shadow-[0_0_10px_rgba(251,191,36,0.5)]';
            $tierIcon = '👑';
        } elseif ($tierPoints >= 100) {
            $tier = 'Silver';
            $nextTier = 'Gold';
            $progress = (int) min(100, round((($tierPoints - 100) / 100) * 100));
            $tierColor = 'text-slate-700 bg-slate-200 border-slate-300 shadow-[0_0_10px_rgba(203,213,225,0.5)]';
            $tierIcon = '⭐';
        } else {
            $tier = 'Bronze';
            $nextTier = 'Silver';
            $progress = (int) min(100, round(($tierPoints / 100) * 100));
            $tierColor = 'text-orange-800 bg-orange-100 border-orange-300 shadow-[0_0_10px_rgba(249,115,22,0.3)]';
            $tierIcon = '🔥';
        }

        return view('dashboard.rewards', compact(
            'me',
            'leaderboard',
            'tier',
            'nextTier',
            'progress',
            'tierColor',
            'tierIcon',
            'tierPoints'
        ));
    }

    public function redeemReward(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reward_title' => ['required', 'string', 'max:120'],
            'cost'         => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();
        $cost = (int) $data['cost'];

        if ((int) $user->points_balance < $cost) {
            return redirect()->route('rewards.index')->with('error', 'Insufficient wellness points balance for this reward.');
        }

        DB::transaction(function () use ($user, $data, $cost): void {
            PointsTransaction::create([
                'user_id'         => $user->id,
                'registration_id' => null,
                'reason'          => 'redeemed_' . str($data['reward_title'])->slug('_'),
                'points'          => -$cost,
            ]);

            $user->decrement('points_balance', $cost);

            DataProcessingLog::record('reward_redeemed', [
                'user_id'      => $user->id,
                'staff_name'   => $user->name,
                'department'   => $user->department,
                'reward_title' => $data['reward_title'],
                'cost'         => $cost,
            ]);
        });

        Cache::forget('hr_overview_data_v21');

        return redirect()->route('rewards.index')
            ->with('success', "Redemption submitted for \"{$data['reward_title']}\" (-{$cost} pts). People & Culture has been notified.");
    }

    public function eventmanagement(): View
    {
        $events = Event::with('createdBy')
            ->withCount('registrations')
            ->orderByDesc('starts_at')
            ->get();

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
                $lowestDim  = $sorted->keys()->last();
            }

            return [
                'categories'     => $categoriesData,
                'eventTable'     => $service->eventTable($category),
                'activeCategory' => $category,
                'highestDim'     => $highestDim,
                'lowestDim'      => $lowestDim,
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

        $windowStart = Carbon::create($cycleStart, 9, 1)->startOfDay();
        $windowEnd   = Carbon::create($cycleEnd, 8, 31)->endOfDay();

        $totalActivePoints = (int) PointsTransaction::whereBetween('created_at', [$windowStart, $windowEnd])
            ->sum('points');

        $points = [
            'current_academic_year' => "{$cycleStart}/{$cycleEnd}",
            'next_reset'            => "31 Aug {$cycleEnd}",
            'total_active_points'   => $totalActivePoints,
        ];

        $activeStaffCount = User::where('role', 'staff')->count();

        return view('hrview.hrdb_settings', [
            'user'             => auth()->user(),
            'cycleStart'       => $cycleStart,
            'cycleEnd'         => $cycleEnd,
            'points'           => $points,
            'activeStaffCount' => $activeStaffCount,
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

        Cache::forget('hr_overview_data_v21');
        Cache::forget('hr_overview_data_v8');
        Cache::forget('hr_impact_data_v6_');

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function resetPoints(Request $request, PointService $pointService): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'string', 'in:RESET,reset'],
        ], [
            'confirmation.in' => 'You must type RESET in the confirmation box to zero staff point balances.',
        ]);

        $count = $pointService->resetAll();

        $month = (int) date('n');
        $year  = (int) date('Y');
        $cycleStart = $month >= 9 ? $year : $year - 1;
        $cycleEnd   = $cycleStart + 1;

        DataProcessingLog::record('points_reset', [
            'users_affected' => $count,
            'academic_year'  => "{$cycleStart}/{$cycleEnd}",
        ]);

        Cache::forget('hr_overview_data_v21');
        Cache::forget('hr_overview_data_v8');

        return redirect()->back()->with('success', "Successfully reset points for {$count} staff members.");
    }
}