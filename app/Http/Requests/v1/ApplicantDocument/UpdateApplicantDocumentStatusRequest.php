<?php
// app/Http/Requests/v1/ApplicantDocument/UpdateApplicantDocumentStatusRequest.php

namespace App\Http\Requests\v1\ApplicantDocument;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantDocumentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add policy check if needed
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    'uploaded',
                    'pending_verification',
                    'under_review',
                    'verified',
                    'rejected',
                    'expired',
                    'requires_correction',
                ]),
            ],
            'rejection_reason' => [
                'nullable',
                'string',
                'max:1000',
                'required_if:status,rejected',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'                  => 'Invalid status value.',
            'rejection_reason.required_if' => 'Rejection reason is required when rejecting a document.',
        ];
    }
}