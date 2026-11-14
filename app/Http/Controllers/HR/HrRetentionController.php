<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use Carbon\Carbon;

class HrRetentionController extends Controller
{
    public function index()
    {
       // CARD COMPUTATIONS 
       
        $totalEventsCount = Event::count();
        $totalTrackedUsers = User::count();

        // 1. Sustained Rate: % of staff who attended 2+ events
        $usersWithTwoPlusEvents = User::whereHas('registrations', function ($q) {
            $q->whereIn('status', ['attended', 'completed', 'checked_in']);
        }, '>=', 2)->count();

        $sustainedRate = $totalTrackedUsers > 0 
            ? round(($usersWithTwoPlusEvents / $totalTrackedUsers) * 100) 
            : 0;

        // 2. Repeat Participants: Total count of staff attending 2+ events
        $repeatParticipantsCount = $usersWithTwoPlusEvents;

        // 3. Avg Loyalty Score: Average events attended per person across the system
        $totalAttendanceLogs = Registration::whereIn('status', ['attended', 'completed', 'checked_in'])->count();
        $avgLoyaltyScore = $totalTrackedUsers > 0 
            ? round($totalAttendanceLogs / $totalTrackedUsers, 1) 
            : 0;

        // 4. Fully Loyal: Staff who attended every single event hosted
        $fullyLoyalCount = 0;
        if ($totalEventsCount > 0) {
            $fullyLoyalCount = User::whereHas('registrations', function ($q) {
                $q->whereIn('status', ['attended', 'completed', 'checked_in']);
            }, '=', $totalEventsCount)->count();
        }


        
        // DROP-OFF GRAPH ANALYSIS (Grouped by Month)
        
        // We look at the last 6 calendar months chronologically
        $chartData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->format('M');
            $year = $monthDate->year;
            $monthNum = $monthDate->month;

            // Total registrations signed up or calendar invites accepted in this month
            $registeredInMonth = Registration::whereHas('event', function ($q) use ($year, $monthNum) {
                $q->whereYear('starts_at', $year)->whereMonth('starts_at', $monthNum);
            })->count();

            // Total who actually checked in or completed the workshop in this month
            $attendedInMonth = Registration::whereHas('event', function ($q) use ($year, $monthNum) {
                $q->whereYear('starts_at', $year)->whereMonth('starts_at', $monthNum);
            })->whereIn('status', ['attended', 'completed', 'checked_in'])->count();

            // Calculate CSS height percentages relative to max value scale (120 baseline)
            $maxScaleValue = 120;
            $regBarHeight = $registeredInMonth > 0 ? min(round(($registeredInMonth / $maxScaleValue) * 100), 100) : 0;
            $attBarHeight = $attendedInMonth > 0 ? min(round(($attendedInMonth / $maxScaleValue) * 100), 100) : 0;

            $chartData->push([
                'month'          => $monthName,
                'registered'     => $registeredInMonth,
                'attended'       => $attendedInMonth,
                'reg_bar_height' => $regBarHeight,
                'att_bar_height' => $attBarHeight,
            ]);
        }


        
        //INDIVIDUAL LOYALTY RECORDS 
        
        // Pull all users, compute their individual attendance ratio data lines
        $usersList = User::all();
        $loyaltyScores = $usersList->map(function ($user) use ($totalEventsCount) {
            $attendedCount = $user->registrations()
                ->whereIn('status', ['attended', 'completed', 'checked_in'])
                ->count();

            $percentage = $totalEventsCount > 0 ? round(($attendedCount / $totalEventsCount) * 100) : 0;

            return [
                'name'        => $user->name,
                'department'  => $user->department ?? 'General Staff',
                'summary'     => "{$attendedCount}/{$totalEventsCount}",
                'percentage'  => $percentage,
            ];
        })->sortBy('percentage'); // Primary sort critical checks to the top!

        // Unique department collection list for our interactive filter selection
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        return view('hrview.hrdb_retention', compact(
            'sustainedRate',
            'repeatParticipantsCount',
            'totalTrackedUsers',
            'avgLoyaltyScore',
            'totalEventsCount',
            'fullyLoyalCount',
            'chartData',
            'loyaltyScores',
            'departments'
        ));
    }
}
