<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\WellbeingResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
        /**
     * HR Coordinator "Overview" dashboard.
     * Calculates the real-time system metrics displayed on your layout cards.
     */
    public function hrOverview(): View
    {
        // 1. COUNT ACCUMULATED HEADCOUNTS: Look at the users table and pull totals
        $totalStaff = User::where('role', 'staff')->count();
        $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();

        // 2. NEW SUSTAINED PARTICIPATION LOGIC
        $sustainedStaffCount = User::where('role', 'staff')
            ->whereHas('registrations', function ($query) {
                $query->where('status', 'completed');
            }, '>=', 2)
            ->count();

        // 2. DYNAMIC ACADEMIC YEAR TIME WINDOW DETERMINATION
        $currentMonth = date('n');
        $currentYear = date('Y');

        if ($currentMonth >= 9) {
            $academicYearStart = "{$currentYear}-09-01 00:00:00";
            $academicYearEnd   = ($currentYear + 1) . "-08-31 23:59:59";
        } else {
            $academicYearStart = ($currentYear - 1) . "-09-01 00:00:00";
            $academicYearEnd   = "{$currentYear}-08-31 23:59:59";
        }

        // 3. FILTER EVENTS BY THE TIME WINDOW
        $eventsThisAcademicYear = Event::whereBetween('created_at', [$academicYearStart, $academicYearEnd])->count();
    
        // 3. COMPUTE SURVEY RESULTS
        $avgLift = round(
            WellbeingResponse::where('stage', 'post')->avg('total_score')
            - WellbeingResponse::where('stage', 'pre')->avg('total_score'),
            1
        );

        // 4. NEW POINTS ACCUMULATION LOGIC
        $totalPointsEarned = User::where('role', 'staff')->sum('points_balance') ?? 0;

               // 5. NEW BAR CHART DISTRIBUTION LOGIC (TOTAL SIGN-UPS BY ACADEMIC YEAR):
        // Counts total sign-up records inside the current academic year window ($academicYearStart to $academicYearEnd)
        
        // Physical Sign-ups
        $physicalCount = \App\Models\Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) {
                $query->where('category', 'physical');
            })->count();

        // Mental Sign-ups
        $mentalCount = \App\Models\Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) {
                $query->where('category', 'mental');
            })->count();

        // Financial Sign-ups
        $financialCount = \App\Models\Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) {
                $query->where('category', 'financial');
            })->count();

        // Social Sign-ups
        $socialCount = \App\Models\Registration::whereBetween('created_at', [$academicYearStart, $academicYearEnd])
            ->whereHas('event', function($query) {
                $query->where('category', 'social');
            })->count();

            
        // 6. NEW DYNAMIC RECENT  LOG QUERY:
        // Pulls the latest 3 registration row changes from the database.
        // It uses with(['user', 'event']) to cleanly load who did what without breaking server speed.
        $recentActivities = \App\Models\Registration::with(['user', 'event'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($reg) {
                // Generates dynamic text and colored dots matching your mockup based on the status
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
                    'time' => $reg->updated_at->diffForHumans(), // Laravel natively turns dates into "2 mins ago"
                    'marker' => $marker
                ];
            });

        // 7. VARIABLES FOR THE BLADE ENGINE
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
            
            // PASSING TO VIEW: Adds our newly created activities log map
            'recentActivities' => $recentActivities, 
        ]);
    }


    /**
     * Staff "Points & Leaderboard" screen.
     * Unchanged — tracks and orders local staff performance metrics.
     */
    public function rewards()
    {
        $me = auth()->user();

        $leaderboard = User::where('role', 'staff')
            ->where('department', $me->department)
            ->orderByDesc('points_balance')
            ->limit(10)
            ->get();

        return view('dashboard.rewards', compact('me', 'leaderboard'));
    }

    /**
     * HR Coordinator "Attendee Table".
     * Unchanged — utilizes eager loading to safely map system transactions without N+1 query limits.
     */
    public function attendees()
    {
        $registrations = \App\Models\Registration::with(['user', 'event', 'waiver', 'checkIn', 'feedback', 'wellbeingResponses'])
            ->latest()
            ->get();

        return view('hrview.hrdb_attendeetable', compact('registrations'));
    }

    // Opens the events management page for HR coordinators
    public function eventmanagement()
    {
        // This opens your events management blade view
        return view('hrview.hrdb_eventsmanagement');
    }

    // Opens the impact analytics page for HR coordinators
    public function impactAnalytics()
    {
        // This opens your impact analytics blade view
        return view('hrview.hrdb_impact');
    }

    // Opens the retention metrics page for HR coordinators
    public function retentionMetrics()
    {
        // This opens your retention metrics blade view
        return view('hrview.hrdb_retention');
    }

    // Opens the departmental analysis page for HR coordinators
    public function departmentAnalysis()
    {
        // This opens your departmental analysis blade view
        return view('hrview.hrdb_departmental');
    }

    //TO BE CHANGED Show the settings page pre-filled with the current user's data. 
    public function settings()
    {
        // Get the logged-in user
        $user = auth()->user(); 
        
        // Pass the user data straight to your blade view
        return view('hrview.hrdb_settings', compact('user'));
    }

    /**
     * Save the updated settings to the database.
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        // 1. Validate the incoming form data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Optional password change
        ]);

        // 2. Update the basic user info
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. If they typed a new password, hash it and update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 4. Save changes to your MySQL database
        $user->save();

        // 5. Send them back with a quick success flash message
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
