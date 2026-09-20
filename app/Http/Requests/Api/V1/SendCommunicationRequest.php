<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SendCommunicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role->canManageEvents();
    }

    public function rules(): array
    {
        return [
            'channel'          => ['required', 'in:email,google_calendar'],
            'recipient_filter' => ['nullable', 'array'],
            'subject'          => ['nullable', 'string', 'max:255'],
            'body'             => ['nullable', 'string', 'max:10000'],
        ];
    }
}