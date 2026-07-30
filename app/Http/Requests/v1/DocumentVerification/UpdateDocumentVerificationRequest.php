<?php

namespace App\Http\Requests\v1\DocumentVerification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('document_verification'));
    }

    public function rules(): array
    {
        return [
            'verification_data' => ['nullable', 'array'],
            'source_data'       => ['nullable', 'array'],
            'total_fields'      => ['sometimes', 'integer', 'min:0'],
            'matched_fields'    => ['sometimes', 'integer', 'min:0'],
            'mismatched_fields' => ['sometimes', 'integer', 'min:0'],
            'missing_fields'    => ['sometimes', 'integer', 'min:0'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'reviewed_by'       => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}