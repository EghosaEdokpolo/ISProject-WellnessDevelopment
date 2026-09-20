<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\RetentionService;
use Illuminate\Http\JsonResponse;

class RetentionController extends Controller
{
    public function __invoke(RetentionService $service): JsonResponse
    {
        return response()->json($service->sustainedParticipation());
    }
}