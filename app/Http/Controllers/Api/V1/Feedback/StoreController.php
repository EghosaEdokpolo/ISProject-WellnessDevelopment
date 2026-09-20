<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFeedbackRequest;
use App\Models\Event;
use App\Models\Feedback;
use App\Services\PointService;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __invoke(StoreFeedbackRequest $request, Event $event, PointService $points): JsonResponse
    {
        $fb = Feedback::updateOrCreate(
            ['event_id' => $event->id, 'staff_id' => $request->user()->id],
            $request->validated()
        );

        $alreadyAwarded = $request->user()->pointsTransactions()
            ->where('event_id', $event->id)
            ->where('reason', 'feedback')
            ->exists();

        if (! $alreadyAwarded) {
            $points->award($request->user(), 5, 'feedback', $event->id);
        }

        return response()->json(['message' => 'Feedback recorded.', 'feedback' => $fb], 201);
    }
}