<?php

namespace App\Http\Controllers\Api\V1\Assessment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAssessmentRequest;
use App\Http\Resources\WellnessAssessmentResource;
use App\Models\Event;
use App\Models\WellnessAssessment;

class StoreController extends Controller
{
    public function __invoke(StoreAssessmentRequest $request, Event $event)
    {
        $assessment = WellnessAssessment::updateOrCreate(
            [
                'event_id'        => $event->id,
                'staff_id'        => $request->user()->id,
                'assessment_type' => $request->assessment_type,
            ],
            [
                'who5_score'  => $request->who5_score,
                'assessed_at' => now(),
            ]
        );

        return (new WellnessAssessmentResource($assessment))
            ->response()
            ->setStatusCode(201);
    }
}