<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $staff = $this->staff;
        $preAssessment  = $staff?->assessments
            ?->firstWhere(fn ($a) => $a->event_id === $this->event_id && $a->assessment_type->value === 'pre');
        $postAssessment = $staff?->assessments
            ?->firstWhere(fn ($a) => $a->event_id === $this->event_id && $a->assessment_type->value === 'post');
        $hasFeedback    = $staff?->feedback
            ?->contains(fn ($f) => $f->event_id === $this->event_id) ?? false;

        return [
            'staff_id'          => $staff?->staff_id_number,
            'name'              => $staff?->fullName(),
            'department'        => $staff?->department?->name,
            'attended'          => (bool) $this->attended,
            'consent'           => (bool) $staff?->consent_given,
            'feedback_provided' => $hasFeedback,
            'who5_pre'          => $preAssessment?->who5_score,
            'who5_post'         => $postAssessment?->who5_score,
            'points'            => $staff?->points_balance,
            'status'            => match (true) {
                $this->attended && $preAssessment && $postAssessment => 'completed',
                $this->attended                                        => 'pending',
                default                                                => 'registered',
            },
        ];
    }
}