<?php
// app/Http/Requests/v1/Applicant/UpdateApplicantStatusRequest.php

namespace App\Http\Requests\v1\Applicant;

use App\Enums\ApplicantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('applicant'));
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(array_column(ApplicantStatus::cases(), 'value')),
            ],
            'rejection_reason' => [
                'nullable',
                'string',
                'max:1000',
                Rule::requiredIf(fn () => $this->input('status') === ApplicantStatus::Rejected->value),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'              => 'Status is required.',
            'status.in'                    => 'Invalid status value.',
            'rejection_reason.required_if' => 'A rejection reason is required when rejecting an applicant.',
        ];
    }
}