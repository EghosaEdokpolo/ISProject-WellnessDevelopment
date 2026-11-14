<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//used as way to track user points
use App\Models\User; 

class HrSettingsController extends Controller
{
    /**
     *  GET SETTINGS DATA
     * This handles the initial page load data.
     */
    public function index()
    {
       //Get the current precise time 
        $today = now(); // Automatically grabs today's real date and time
        $currentYear = (int)$today->format('Y'); 
        $currentMonth = (int)$today->format('n');

        // Compute the dynamic academic balance boundaries
        if ($currentMonth >= 9) {
            // It is September or later: Cycle began this year and ends next year
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        } else {
            // It is between Jan and August: Cycle began last year and ends this year
            $startYear = $currentYear - 1;
            $endYear = $currentYear;
        }

        // Format the text string combinations dynamically
        $academicYearString = "{$startYear}/{$endYear}"; 
        
        // Next reset is always August 31st at the finish line of the academic cycle
        $nextResetString = "31st August " . $endYear; 


        $totalPointsEarned = \App\Models\User::where('role', 'staff')->sum('points_balance') ?? 0;

        $points = [
            'current_academic_year' => $academicYearString,
            'next_reset' => $nextResetString,
            'total_active_points' => $totalPointsEarned 
            
        ];

        // this can be fetched dynamically from a database or config file.
        // We return a JSON response matching what your JavaScript expects.
        $dataProtection = [
            'controller' => 'Strathmore University ICT & Compliance Office',
            'applicable_law' => 'Kenya Data Protection Act, 2019',
            'retention_period' => '7 Years Post-Graduation/Exit',
            'contact' => 'dataprotection@strathmore.edu',
            'dpia_status' => 'Compliant (Last Audit: Jan 2026)',
        ];

        // We load the view file, and use compact() to inject our data variables into it!
        return view('hrview.hrdb_settings', compact('points', 'dataProtection'));
    }

    /**
     *  RESET STAFF POINTS
     * This handles the POST request when clicking "Reset All Staff Points".
     */
    public function resetPoints()
    {
        try {
            //  In Laravel, Eloquent makes updating records simple.
            // This query says: "Find all users where role is staff, and update their points to 0".
            // $staffAffected = User::where('role', 'staff')->update(['wellness_points' => 0]);
        $staffAffected = User::where('role', 'staff')
                             ->update(['points_balance' => 0]); 

        return response()->json([
            'success' => true,
            'message' => 'All staff wellness points have been zeroed out successfully.',
            'staff_affected' => $staffAffected // This will now show your REAL count (e.g. 5, 12, etc.)
        ], 200);

        // If something goes wrong (e.g., database connection down), return an error        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * LOGOUT FUNCTION
     * This handles destroying the user session cleanly.
     */
    public function logout(Request $request)
    {
        // This logs out the authenticated user.
        Auth::logout();

        // Security best practice: Invalidate the user's session token 
        // to prevent anyone else from reusing it.
        $request->session()->invalidate();

        // Regenerate the user session token to prevent cross-site request forgery attacks.
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
