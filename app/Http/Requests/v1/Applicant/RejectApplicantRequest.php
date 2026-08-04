<?php
// app/Http/Requests/v1/Applicant/RejectApplicantRequest.php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class RejectApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('applicant'));
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Please provide a reason for rejection.',
            'rejection_reason.min'      => 'Rejection reason must be at least 5 characters.',
        ];
    }
}