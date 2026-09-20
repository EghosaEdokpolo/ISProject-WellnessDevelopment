<?php

namespace App\Http\Controllers\Api\V1\Points;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingLog;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResetController extends Controller
{
    public function __invoke(Request $request, PointService $points): JsonResponse
    {
        abort_unless($request->user()->role->canResetPoints(), 403);

        $count = $points->resetAll();

        DataProcessingLog::record('points_reset', ['staff_affected' => $count]);

        return response()->json([
            'message'         => 'Points reset complete.',
            'staff_affected'  => $count,
        ]);
    }
}