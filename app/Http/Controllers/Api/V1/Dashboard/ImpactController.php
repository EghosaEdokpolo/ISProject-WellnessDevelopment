<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\WellnessImpactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImpactController extends Controller
{
    public function __invoke(Request $request, WellnessImpactService $service): JsonResponse
    {
        $category = $request->query('category');

        return response()->json([
            'categories'   => $service->categoryBreakdown($category),
            'event_table'  => $service->eventTable($category),
        ]);
    }
}