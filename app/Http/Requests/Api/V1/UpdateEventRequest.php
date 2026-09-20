<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\WellnessCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role->canManageEvents();
    }

    public function rules(): array
    {
        return [
            'title'             => ['sometimes', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:5000'],
            'wellness_category' => ['sometimes', new Enum(WellnessCategory::class)],
            'starts_at'         => ['sometimes', 'date'],
            'ends_at'           => ['nullable', 'date'],
            'venue'             => ['nullable', 'string', 'max:255'],
            'capacity'          => ['nullable', 'integer', 'min:1'],
            'points_awarded'    => ['nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }
}