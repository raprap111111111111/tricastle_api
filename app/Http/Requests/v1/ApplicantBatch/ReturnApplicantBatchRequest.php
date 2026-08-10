<?php

namespace App\Http\Requests\v1\ApplicantBatch;

use Illuminate\Foundation\Http\FormRequest;

class ReturnApplicantBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust with policy if needed
    }

    public function rules(): array
    {
        return [
            'return_reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_reason.required' => 'Please provide a reason for the applicant returning home.',
            'return_reason.min'      => 'Return reason must be at least 3 characters.',
        ];
    }
}