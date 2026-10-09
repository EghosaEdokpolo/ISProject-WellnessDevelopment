<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HrRetentionController extends Controller
{
    /**
     * Display the Sustained Retention Metrics Dashboard.
     */
    public function index(): View
    {
        // Points directly to your retention blade layout template
        return view('hrview.hrdb_retention'); 
    }
}
