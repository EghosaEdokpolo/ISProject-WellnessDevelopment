<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\AssessmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'assessment_type' => ['required', new Enum(AssessmentType::class)],
            'who5_score'      => ['required', 'integer', 'min:0', 'max:25'],
        ];
    }
}