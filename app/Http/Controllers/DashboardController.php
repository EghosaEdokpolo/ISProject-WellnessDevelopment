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
        $events        = Event::orderByDesc('starts_at')->get();
        $staffUsers    = User::where('role', 'staff')->orderBy('name')->get();

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

        $event   = Event::findOrFail($data['event_id']);
        $user    = User::findOrFail($data['user_id']);
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
                $base     = intdiv($preTotal, 5);
                $rem      = $preTotal % 5;

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
                $base      = intdiv($postTotal, 5);
                $rem       = $postTotal % 5;

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
                $checkinPts = (int) ($event->points_checkin ?? 25);
                if (! PointsTransaction::where('registration_id', $registration->id)->where('reason', 'attended_event')->exists()) {
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

    /**
     * Step 3: Live Rewards Standing, Department Leaderboard Switcher, & Event-Synced Recent Activity Feed.
     */
    public function rewards(Request $request): View
    {
        $me = $request->user();
        $tz = (string) config('wellloop.timezone', 'Africa/Nairobi');

        // Backfill any missing attendance or feedback point transactions for seeded/verified registrations
        $myRegs = Registration::with(['event', 'checkIn', 'feedback', 'waiver'])
            ->where('user_id', $me->id)
            ->get()
            ->filter(fn (Registration $r): bool => $r->event !== null);

        foreach ($myRegs as $reg) {
            $evStart = $reg->event->starts_at ?? now();

            if ($reg->checkIn !== null || in_array($reg->status, ['checked_in', 'completed'], true)) {
                $existsAtt = PointsTransaction::where('registration_id', $reg->id)
                    ->where('reason', 'attended_event')
                    ->exists();

                if (! $existsAtt) {
                    $tx = PointsTransaction::create([
                        'user_id'         => $me->id,
                        'registration_id' => $reg->id,
                        'reason'          => 'attended_event',
                        'points'          => (int) ($reg->event->points_checkin ?? 25),
                    ]);
                    $tx->timestamps = false;
                    $tx->created_at = $reg->checkIn?->checked_in_at ?? $evStart;
                    $tx->updated_at = $reg->checkIn?->checked_in_at ?? $evStart;
                    $tx->save();
                }
            }

            if ($reg->status === 'completed' || $reg->feedback !== null) {
                $existsFb = PointsTransaction::where('registration_id', $reg->id)
                    ->where('reason', 'feedback_submitted')
                    ->exists();

                if (! $existsFb) {
                    $tx = PointsTransaction::create([
                        'user_id'         => $me->id,
                        'registration_id' => $reg->id,
                        'reason'          => 'feedback_submitted',
                        'points'          => (int) ($reg->event->points_feedback ?? 10),
                    ]);
                    $tx->timestamps = false;
                    $tx->created_at = $reg->feedback?->created_at ?? $evStart->copy()->addHours(2);
                    $tx->updated_at = $reg->feedback?->created_at ?? $evStart->copy()->addHours(2);
                    $tx->save();
                }
            }
        }

        // Recalculate accurate live points balance from ledger
        $netLedgerBalance = (int) PointsTransaction::where('user_id', $me->id)->sum('points');
        if ($netLedgerBalance >= 0 && (int) $me->points_balance !== $netLedgerBalance) {
            $me->update(['points_balance' => $netLedgerBalance]);
        }

        // Department Leaderboard with Switcher Support
        $allDepartments = User::where('role', 'staff')
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->all();

        $selectedDept = (string) $request->query('dept', (string) $me->department);

        $leaderboardQuery = User::where('role', 'staff')
            ->orderByDesc('points_balance')
            ->orderBy('name');

        if ($selectedDept !== '' && $selectedDept !== 'all') {
            $leaderboardQuery->where('department', $selectedDept);
        }

        $leaderboard = $leaderboardQuery->limit(10)->get();

        // Use cumulative earned points for Appraisal Tier standing so redeeming rewards doesn't demote staff
        $cumulativeEarned = (int) PointsTransaction::where('user_id', $me->id)
            ->where('points', '>', 0)
            ->sum('points');
        $tierPoints = max((int) $me->points_balance, $cumulativeEarned);

        if ($tierPoints >= 200) {
            $tier      = 'Gold';
            $nextTier  = 'Max Level';
            $progress  = 100;
            $tierColor = 'text-amber-800 bg-amber-100 border-amber-300 shadow-[0_0_10px_rgba(251,191,36,0.5)]';
            $tierIcon  = '👑';
        } elseif ($tierPoints >= 100) {
            $tier      = 'Silver';
            $nextTier  = 'Gold';
            $progress  = (int) min(100, round((($tierPoints - 100) / 100) * 100));
            $tierColor = 'text-slate-700 bg-slate-200 border-slate-300 shadow-[0_0_10px_rgba(203,213,225,0.5)]';
            $tierIcon  = '⭐';
        } else {
            $tier      = 'Bronze';
            $nextTier  = 'Silver';
            $progress  = (int) min(100, round(($tierPoints / 100) * 100));
            $tierColor = 'text-orange-800 bg-orange-100 border-orange-300 shadow-[0_0_10px_rgba(249,115,22,0.3)]';
            $tierIcon  = '🔥';
        }

        // Map registrations by ID so Recent Activity works even without eager-loading relationships
        $regsById = $myRegs->keyBy('id');

        $recentTransactions = PointsTransaction::where('user_id', $me->id)
            ->get()
            ->map(function (PointsTransaction $tx) use ($regsById, $tz): array {
                $reg       = $tx->registration_id ? $regsById->get((int) $tx->registration_id) : null;
                $event     = $reg?->event;
                $eventName = $event?->name;

                $activityDate = $tx->created_at?->copy()->shiftTimezone($tz) ?? now($tz);
                if ($event && $event->starts_at) {
                    $evDate = $event->starts_at->copy()->shiftTimezone($tz);
                    if ($tx->reason === 'attended_event') {
                        $activityDate = $reg->checkIn?->checked_in_at?->copy()->shiftTimezone($tz) ?? $evDate;
                    } elseif ($tx->reason === 'feedback_submitted' && $evDate->lt($activityDate)) {
                        $activityDate = $evDate->copy()->addHours(2);
                    } elseif ($tx->reason === 'waiver_signed' && $evDate->lt($activityDate)) {
                        $activityDate = $evDate->copy()->subDay();
                    }
                }

                $title = match ($tx->reason) {
                    'attended_event'     => 'Venue Check-In Verified',
                    'feedback_submitted' => 'Post-Event WHO-5 Submitted',
                    'waiver_signed'      => 'Event Registration & Waiver',
                    default              => str_starts_with((string) $tx->reason, 'redeemed_')
                        ? 'Reward Redeemed: ' . ucwords(str_replace(['redeemed_', '_'], ['', ' '], (string) $tx->reason))
                        : ucwords(str_replace('_', ' ', (string) $tx->reason)),
                };

                return [
                    'title'      => $title,
                    'event_name' => $eventName,
                    'reason'     => (string) $tx->reason,
                    'points'     => (int) $tx->points,
                    'date_str'   => $activityDate->format('d M Y, h:i A'),
                    'sort_ts'    => $activityDate->timestamp,
                ];
            })
            ->sortByDesc('sort_ts')
            ->take(10)
            ->values();

        return view('dashboard.rewards', compact(
            'me',
            'leaderboard',
            'allDepartments',
            'selectedDept',
            'tier',
            'nextTier',
            'progress',
            'tierColor',
            'tierIcon',
            'tierPoints',
            'recentTransactions'
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
        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $events = Event::withCount(['registrations', 'checkIns'])
            ->orderByDesc('starts_at')
            ->get();

        return view('hrview.hrdb_eventsmanagement', [
            'events' => $events,
            'cutoff' => $cutoff,
        ]);
    }

    public function impactAnalytics(WellnessImpactService $service): View
    {
        $category       = request('category');
        $categoriesData = $service->categoryBreakdown($category);
        $highestDim     = null;
        $lowestDim      = null;

        if ($category === null && count($categoriesData) > 0) {
            $withData = collect($categoriesData)->filter(fn ($item) => ($item['responses'] ?? 0) > 0);
            $pool     = $withData->isNotEmpty() ? $withData : collect($categoriesData);
            $sorted   = $pool->sortByDesc('avg_lift');

            $highestDim = $sorted->keys()->first();
            $lowestDim  = $sorted->keys()->last();
        }

        return view('hrview.hrdb_impact', [
            'categories'     => $categoriesData,
            'eventTable'     => $service->eventTable($category),
            'activeCategory' => $category,
            'highestDim'     => $highestDim,
            'lowestDim'      => $lowestDim,
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

    public function settings(): View
    {
        $month = (int) date('n');
        $year  = (int) date('Y');

        $cycleStart = $month >= 9 ? $year : $year - 1;
        $cycleEnd   = $cycleStart + 1;
        $semester   = ($month >= 9 || $month <= 1) ? 1 : 2;

        $liveStaffBalanceSum = (int) User::where('role', 'staff')->sum('points_balance');
        $positiveTxSum       = (int) PointsTransaction::where('points', '>', 0)->sum('points');
        $totalActivePoints   = $liveStaffBalanceSum > 0 ? $liveStaffBalanceSum : $positiveTxSum;

        $points = [
            'current_academic_year' => "{$cycleStart}/{$cycleEnd}",
            'current_cycle_label'   => "Semester {$semester}, {$year}",
            'next_reset'            => "31 Jan {$cycleEnd}",
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

        $month      = (int) date('n');
        $year       = (int) date('Y');
        $cycleStart = $month >= 9 ? $year : $year - 1;
        $cycleEnd   = $cycleStart + 1;

        DataProcessingLog::record('points_reset', [
            'users_affected' => $count,
            'academic_year'  => "{$cycleStart}/{$cycleEnd}",
        ]);

        Cache::forget('hr_overview_data_v21');

        return redirect()->back()->with('success', "Successfully zeroed wellness points for {$count} staff members.");
    }
}