<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HrDepartmentController extends Controller
{
    /**
     * Display the Departmental Analysis Dashboard.
     */
    public function index(): View
    {
        // Points directly to your departmental analysis blade layout template
        return view('hrview.hrdb_departmental'); 
    }
}
