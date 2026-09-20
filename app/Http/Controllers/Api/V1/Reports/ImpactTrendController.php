<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Services\WellnessImpactService;
use Illuminate\Http\Request;

class ImpactTrendController extends Controller
{
    public function __invoke(Request $request, WellnessImpactService $service)
    {
        return response()->json([
            'categories'  => $service->categoryBreakdown($request->category),
            'event_table' => $service->eventTable($request->category),
        ]);
    }
}