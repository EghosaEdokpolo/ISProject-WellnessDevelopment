<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\WellbeingResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // HR Coordinator "Overview" dashboard
    public function hrOverview(): View
    {
        $totalStaff = User::where('role', 'staff')->count();
        $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();

        // avg WHO-5 lift = average(post_score - pre_score) across everyone who has both
        $avgLift = round(
            WellbeingResponse::where('stage', 'post')->avg('total_score')
            - WellbeingResponse::where('stage', 'pre')->avg('total_score'),
            1
        );

        return view('dashboard.hr', [
            'registrationRate' => $totalStaff ? round($registeredStaff / $totalStaff * 100, 1) : 0,
            'avgLift' => $avgLift ?: 0,
            'eventsThisSemester' => Event::count(),
        ]);
    }

    // Staff "Points & Leaderboard" screen
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

    // HR Coordinator "Attendee Table" — mirrors your prototype's full
    // participation record: who registered, who's checked in, who's given
    // feedback, and their WHO-5 pre/post scores.
    public function attendees()
    {
        // POINTER: with(['user', 'event', 'waiver', 'checkIn', 'feedback',
        // 'wellbeingResponses']) is "eager loading" — it fetches all these
        // related rows in a handful of queries up front, instead of Laravel
        // running a fresh query for every single relationship access inside
        // the Blade @foreach below (which would be dozens of tiny queries
        // for a real attendee list — the classic "N+1 query problem").
        $registrations = \App\Models\Registration::with(['user', 'event', 'waiver', 'checkIn', 'feedback', 'wellbeingResponses'])
            ->latest()
            ->get();

        return view('dashboard.attendees', compact('registrations'));
    }
}
