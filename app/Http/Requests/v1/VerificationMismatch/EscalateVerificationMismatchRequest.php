<?php

namespace App\Http\Requests\v1\VerificationMismatch;

use Illuminate\Foundation\Http\FormRequest;

class EscalateVerificationMismatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('escalate', $this->route('verification_mismatch'));
    }

    public function rules(): array
    {
        return [
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}