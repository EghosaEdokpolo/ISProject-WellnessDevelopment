<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\WellbeingResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
       

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
