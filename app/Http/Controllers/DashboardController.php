<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\WellbeingResponse;
use App\Models\Feedback;
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

        // ALL 8 SAMHSA WELLNESS DIMENSIONS FOR THE GRAPH
        
        // --- 1. REGISTRATION COUNTS ---
        $regPhysical      = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'physical'))->count();
        $regMental        = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'mental'))->count();
        $regFinancial     = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'financial'))->count();
        $regSocial        = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'social'))->count();
        $regIntellectual  = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'intellectual'))->count();
        $regOccupational  = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'occupational'))->count();
        $regEnvironmental = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'environmental'))->count();
        $regSpiritual     = Registration::whereBetween('created_at', [$start, $end])->whereHas('event', fn ($q) => $q->where('category', 'spiritual'))->count();

        // --- 2. ATTENDANCE COUNTS (Status check matches your database snippet) ---
        $attStatuses = ['checked_in', 'completed'];
        $attPhysical      = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'physical'))->count();
        $attMental        = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'mental'))->count();
        $attFinancial     = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'financial'))->count();
        $attSocial        = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'social'))->count();
        $attIntellectual  = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'intellectual'))->count();
        $attOccupational  = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'occupational'))->count();
        $attEnvironmental = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'environmental'))->count();
        $attSpiritual     = Registration::whereBetween('created_at', [$start, $end])->whereIn('status', $attStatuses)->whereHas('event', fn ($q) => $q->where('category', 'spiritual'))->count();

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

        // NEW STAKEHOLDER FEEDBACK METRICS:
        $mostAttendedEvent = Registration::with('event')
            ->selectRaw('event_id, count(*) as total')
            ->groupBy('event_id')
            ->orderByDesc('total')
            ->first();

        $qualitativeFeedback = Feedback::with(['registration.user', 'registration.event'])
            ->whereNotNull('comments')
            ->where('comments', '!=', '')
            ->latest()
            ->take(5)
            ->get();
     
        //METRIC CARD 2: UPCOMING CONSENT COMPLETION RATE (1-Month Look-Ahead Window)
            
            // Get total registrations for events starting in the next 30 days
            $totalUpcomingRegistrations = Registration::whereHas('event', function ($q) {
                $q->where('starts_at', '>=', now())
                ->where('starts_at', '<=', now()->addDays(30));
            })->count();

            // Count how many of those have a matching row inside the waivers table
            $completedUpcomingWaivers = Registration::whereHas('event', function ($q) {
                $q->where('starts_at', '>=', now())
                ->where('starts_at', '<=', now()->addDays(30));
            })->whereHas('waiver')->count(); // Looks for the registration_id match automatically!

            // If there are no upcoming registrations, show 100% compliance as a fallback
            $consentCompletionRate = $totalUpcomingRegistrations 
                ? round(($completedUpcomingWaivers / $totalUpcomingRegistrations) * 100, 1) 
                : 0;

        
        // METRIC 3: SURVEY VELOCITY RATE (1-Month Look-Back Cut-off Window)
        
            // Count total feedback forms submitted in the last 30 days
            $totalRecentFeedback = Feedback::where('created_at', '>=', now()->subDays(30))->count();

            // Count how many were submitted within 24 hours of the event starting
            $fastFeedbackCount = Feedback::where('created_at', '>=', now()->subDays(30))
            ->whereHas('registration.event', function ($q) {
                // Fixed syntax: Uses pure column identifiers without nested parenthetical quotes
                $q->whereRaw('feedback.created_at <= DATE_ADD(events.starts_at, INTERVAL 1 DAY)');
                    })->count();

            $surveyVelocityRate = $totalRecentFeedback 
                ? round(($fastFeedbackCount / $totalRecentFeedback) * 100, 1) 
                : 0;

        return view('hrview.hrdb_overview', [
            'registrationRate' => $totalStaff ? round($registeredStaff / $totalStaff * 100, 1) : 0,
            'sustainedParticipationRate' => $totalStaff ? round($sustainedStaffCount / $totalStaff * 100, 1) : 0,
            'consentCompletionRate' => $consentCompletionRate,
            'surveyVelocityRate' => $surveyVelocityRate,
            'avgLift' => $avgLift ?: 0,
            'registeredStaff' => $registeredStaff,
            'totalStaff' => $totalStaff,
            'totalPointsEarned' => $totalPointsEarned,

            // DYNAMIC BAR CHART PAYLOAD (REGISTERED VALUES)
            'regPhysical'         => $regPhysical,
            'regMental'           => $regMental,
            'regFinancial'        => $regFinancial,
            'regSocial'           => $regSocial,
            'regIntellectual'     => $regIntellectual,
            'regOccupational'     => $regOccupational,
            'regEnvironmental'    => $regEnvironmental,
            'regSpiritual'        => $regSpiritual,
            
            // DYNAMIC BAR CHART PAYLOAD (ATTENDED VALUES)
            'attPhysical'         => $attPhysical,
            'attMental'           => $attMental,
            'attFinancial'        => $attFinancial,
            'attSocial'           => $attSocial,
            'attIntellectual'     => $attIntellectual,
            'attOccupational'     => $attOccupational,
            'attEnvironmental'    => $attEnvironmental,
            'attSpiritual'        => $attSpiritual,

            'eventsThisSemester' => $eventsThisAcademicYear,
            'recentActivities' => $recentActivities,
            'mostAttendedEvent' => $mostAttendedEvent,
            'qualitativeFeedback' => $qualitativeFeedback,
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


    public function retentionMetrics(RetentionService $service): View
    {
        return view('hrview.hrdb_retention', ['retention' => $service->sustainedParticipation()]);
    }

    public function departmentAnalysis(DepartmentalEngagementService $service): View
    {
        return view('hrview.hrdb_departmental', ['ranking' => $service->ranking()]);
    }

    // public function settings()
    // {
    //     return view('hrview.hrdb_settings', ['user' => auth()->user()]);
    // }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $request->validate(['name' => 'required|string|max:255', 'password' => 'nullable|string|min:8|confirmed']);
        $user->name = $request->name;
        if ($request->filled('password')) { $user->password = bcrypt($request->password); }
        $user->save();
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }


  
    //qualitative feedback logs controller method
    public function feedbackLogs(Request $request)
    {
        // 1. Fetch all events from the database to populate our filter dropdown menu
        $events = Event::orderByDesc('starts_at')->get();

        // 2. Check if the user selected a specific event from the dropdown filter
        $selectedEventId = $request->input('event_id');

        // 3. Build a dynamic Eloquent database query. start by looking at all feedback entries that contain written text comments
        $query = Feedback::with(['registration.user', 'registration.event'])
            ->whereNotNull('comments')
            ->where('comments', '!=', '');

        // 4. THE STEP-BY-STEP FILTER LOGIC:
        if ($selectedEventId) {
            $query->whereHas('registration', function ($q) use ($selectedEventId) {
                $q->where('event_id', $selectedEventId);
            });
        }

        // 5. Execute the query and sort by the newest feedback entries first
        $allFeedback = $query->latest()->get();

        // 6. Return the clean view layout file and inject our data variables into it
        return view('hrview.hrdb_feedback_logs', compact('events', 'allFeedback', 'selectedEventId'));
    }

}