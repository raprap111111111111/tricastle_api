<?php

namespace App\Http\Requests\v1\DocumentVerification;

use Illuminate\Foundation\Http\FormRequest;

class CompleteDocumentVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('complete', $this->route('document_verification'));
    }

    public function rules(): array
    {
        return [
            'total_fields'      => ['required', 'integer', 'min:1'],
            'matched_fields'    => ['required', 'integer', 'min:0'],
            'mismatched_fields' => ['required', 'integer', 'min:0'],
            'missing_fields'    => ['required', 'integer', 'min:0'],
            'verification_data' => ['nullable', 'array'],
            'source_data'       => ['nullable', 'array'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ];
    }
}