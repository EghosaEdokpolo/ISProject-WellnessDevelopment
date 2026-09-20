<?php

namespace App\Http\Controllers\Api\V1\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\WellnessImpactService;
use Illuminate\Http\JsonResponse;

class SummaryController extends Controller
{
    public function __invoke(Event $event, WellnessImpactService $service): JsonResponse
    {
        return response()->json($service->impactForEvent($event->id));
    }
}