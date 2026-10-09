<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HrEventsController extends Controller
{
    /**
     * Display the Attendees Dashboard.
     */
    public function index(): View
    {
        // Points directly to your attendees blade layout template
        return view('hrview.hrdb_eventsmanagement'); 
    }
}
