<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DepartmentalEngagementService;
use Illuminate\Http\JsonResponse;

class DepartmentalController extends Controller
{
    public function __invoke(DepartmentalEngagementService $service): JsonResponse
    {
        return response()->json($service->ranking());
    }
}