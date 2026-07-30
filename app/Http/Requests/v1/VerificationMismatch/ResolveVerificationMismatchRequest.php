<?php

namespace App\Http\Requests\v1\VerificationMismatch;

use Illuminate\Foundation\Http\FormRequest;

class ResolveVerificationMismatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('resolve', $this->route('verification_mismatch'));
    }

    public function rules(): array
    {
        return [
            'resolution_notes' => ['required', 'string', 'max:1000'],
        ];
    }
}