<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WellnessAssessmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'event_id'        => $this->event_id,
            'staff_id'        => $this->staff_id,
            'assessment_type' => $this->assessment_type->value,
            'who5_score'      => $this->who5_score,
            'assessed_at'     => $this->assessed_at->toIso8601String(),
        ];
    }
}