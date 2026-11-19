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
use App\Services\RetentionService;
use App\Services\WellnessImpactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function exportAttendeesCsv(Request $request): StreamedResponse
    {
        $query = Registration::with(['user', 'event', 'waiver', 'checkIn', 'feedback', 'wellbeingResponses'])
            ->whereHas('user')
            ->whereHas('event');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->whereHas('user', function ($q) use ($search): void {
                $q->where(fn ($sub) => $sub
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%"));
            });
        }

        $rows     = $query->latest('updated_at')->get();
        $filename = 'Strathmore-WellLoop-Attendees-' . now('Africa/Nairobi')->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Staff ID', 'Staff Name', 'School / Department', 'Event', 'Event Date',
                'Attended', 'Waiver Consent', 'Feedback Submitted', 'WHO-5 Pre', 'WHO-5 Post',
                'WHO-5 Lift', 'Current Points', 'Status',
            ]);

            foreach ($rows as $reg) {
                $pre  = $reg->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->first();
                $post = $reg->wellbeingResponses->firstWhere('stage', 'post');
                $lift = ($pre && $post) ? ((int) $post->total_score - (int) $pre->total_score) : '';

                fputcsv($out, [
                    $reg->user->staff_id,
                    $reg->user->name,
                    $reg->user->department,
                    $reg->event->name,
                    $reg->event->starts_at?->format('Y-m-d H:i') ?? '',
                    ($reg->checkIn || in_array($reg->status, ['checked_in', 'completed'], true)) ? 'Yes' : 'No',
                    ($reg->waiver || $reg->consent_given) ? 'Yes' : 'No',
                    ($reg->feedback || $reg->status === 'completed') ? 'Yes' : 'No',
                    $pre->total_score ?? '',
                    $post->total_score ?? '',
                    $lift,
                    (int) ($reg->user->points_balance ?? 0),
                    strtoupper(str_replace('_', ' ', (string) $reg->status)),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
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
                $checkinPts = (int) ($event->points_checkin ?? 20);
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

        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));
        $prefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($prefix);
        Cache::forget($prefix . $event->category);

        return redirect()->route('attendees.index')
            ->with('success', "Historical attendance logged for {$user->name} ({$event->name}).");
    }

    public function rewards(Request $request): View
    {
        $me = $request->user();
        $tz = (string) config('wellloop.timezone', 'Africa/Nairobi');

        $myRegs = Registration::with(['event', 'checkIn', 'feedback', 'waiver'])
            ->where('user_id', $me->id)
            ->get()
            ->filter(fn (Registration $r): bool => $r->event !== null);

        // Strictly scoped to the logged-in staff member's own school/department
        $leaderboard = User::where('role', 'staff')
            ->where('department', $me->department)
            ->orderByDesc('points_balance')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Lifetime points (all positive ledger entries) preserve Appraisal Tier
        $cumulativeEarned = (int) PointsTransaction::where('user_id', $me->id)
            ->where('points', '>', 0)
            ->sum('points');
        $tierPoints = max((int) $me->points_balance, $cumulativeEarned);

        if ($tierPoints >= 200) {
            $tier      = 'Gold';
            $nextTier  = 'Max Level';
            $progress  = 100;
            $tierColor = 'text-amber-800 bg-amber-100 border-amber-300';
            $tierIcon  = '👑';
        } elseif ($tierPoints >= 100) {
            $tier      = 'Silver';
            $nextTier  = 'Gold';
            $progress  = (int) min(100, round((($tierPoints - 100) / 100) * 100));
            $tierColor = 'text-slate-700 bg-slate-200 border-slate-300';
            $tierIcon  = '⭐';
        } else {
            $tier      = 'Bronze';
            $nextTier  = 'Silver';
            $progress  = (int) min(100, round(($tierPoints / 100) * 100));
            $tierColor = 'text-orange-800 bg-orange-100 border-orange-300';
            $tierIcon  = '🔥';
        }

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
                    'annual_cycle_reset' => 'Annual Academic Cycle Reset',
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

        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));

        return redirect()->route('rewards.index')
            ->with('success', "Redemption submitted for \"{$data['reward_title']}\" (-{$cost} pts). People & Culture has been notified.");
    }

    public function eventmanagement(): View
    {
        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 15);
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

        // Chronological Monthly WHO-5 Pre/Post aggregation (respects active category filter)
        $tz = (string) config('wellloop.timezone', 'Africa/Nairobi');

        $regsQuery = Registration::with(['event', 'wellbeingResponses'])
            ->whereHas('event', function ($q) use ($category): void {
                $q->whereNotNull('starts_at');
                if ($category !== null && $category !== '') {
                    $q->where('category', strtolower((string) $category));
                }
            });

        $buckets = [];

        foreach ($regsQuery->get() as $reg) {
            $pre  = $reg->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->first();
            $post = $reg->wellbeingResponses->firstWhere('stage', 'post');

            if (! $pre || ! $post || ! $reg->event?->starts_at) {
                continue;
            }

            $eventDate = $reg->event->starts_at->copy()->shiftTimezone($tz);
            $monthKey  = $eventDate->format('Y-m');

            if (! isset($buckets[$monthKey])) {
                $buckets[$monthKey] = [
                    'key'       => $monthKey,
                    'year'      => $eventDate->format('Y'),
                    'short'     => $eventDate->format('M'),
                    'full'      => $eventDate->format('F Y'),
                    'pre_sum'   => 0.0,
                    'post_sum'  => 0.0,
                    'responses' => 0,
                    'event_ids' => [],
                ];
            }

            $buckets[$monthKey]['pre_sum']   += (float) $pre->total_score;
            $buckets[$monthKey]['post_sum']  += (float) $post->total_score;
            $buckets[$monthKey]['responses'] += 1;
            $buckets[$monthKey]['event_ids'][$reg->event_id] = true;
        }

        ksort($buckets);

        $distinctYears = collect($buckets)->pluck('year')->unique()->count();

        $monthlyTrend = collect($buckets)->map(function (array $b) use ($distinctYears): array {
            $count   = max(1, $b['responses']);
            $preAvg  = round($b['pre_sum'] / $count, 1);
            $postAvg = round($b['post_sum'] / $count, 1);

            return [
                'month'        => $distinctYears > 1 ? ($b['short'] . " '" . substr($b['year'], -2)) : $b['short'],
                'full_label'   => $b['full'],
                'pre_avg'      => $preAvg,
                'post_avg'     => $postAvg,
                'lift'         => round($postAvg - $preAvg, 1),
                'responses'    => $b['responses'],
                'events_count' => count($b['event_ids']),
            ];
        })->values()->all();

        return view('hrview.hrdb_impact', [
            'categories'     => $categoriesData,
            'eventTable'     => $service->eventTable($category),
            'monthlyTrend'   => $monthlyTrend,
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
        $month      = (int) date('n');
        $year       = (int) date('Y');
        $cycleStart = $month >= 9 ? $year : $year - 1;
        $cycleEnd   = $cycleStart + 1;
        $semester   = ($month >= 9 || $month <= 1) ? 1 : 2;

        $totalActivePoints = (int) User::where('role', 'staff')->sum('points_balance');

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

    /**
     * Future-proof Double-Entry Ledger Reset:
     * Inserts an offsetting negative transaction so spendable balances become 0 while preserving full audit & lifetime history.
     */
    public function resetPoints(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'string', 'in:RESET,reset'],
        ], [
            'confirmation.in' => 'You must type RESET in the confirmation box to zero staff point balances.',
        ]);

        $affected = 0;

        DB::transaction(function () use (&$affected): void {
            $staffWithPoints = User::where('role', 'staff')
                ->where('points_balance', '>', 0)
                ->get();

            foreach ($staffWithPoints as $staff) {
                $balance = (int) $staff->points_balance;

                PointsTransaction::create([
                    'user_id'         => $staff->id,
                    'registration_id' => null,
                    'reason'          => 'annual_cycle_reset',
                    'points'          => -$balance,
                ]);

                $staff->update(['points_balance' => 0]);
                $affected++;
            }
        });

        $month      = (int) date('n');
        $year       = (int) date('Y');
        $cycleStart = $month >= 9 ? $year : $year - 1;
        $cycleEnd   = $cycleStart + 1;

        DataProcessingLog::record('points_reset', [
            'users_affected' => $affected,
            'academic_year'  => "{$cycleStart}/{$cycleEnd}",
        ]);

        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));

        return redirect()->back()->with('success', "Annual cycle reset complete: zeroed spendable balances for {$affected} staff members while preserving historical lifetime points.");
    }
}