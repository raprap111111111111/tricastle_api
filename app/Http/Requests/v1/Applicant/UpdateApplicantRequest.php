<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit_any_applicant')
            || $this->user()?->can('edit_own_applicant');
    }

    public function rules(): array
    {
        $applicantId = $this->route('applicant')?->id;

        return [
            // Personal
            'first_name'         => ['nullable', 'string', 'max:255'],
            'middle_name'        => ['nullable', 'string', 'max:255'],
            'last_name'          => ['nullable', 'string', 'max:255'],
            'suffix'             => ['nullable', 'string', 'max:10'],
            'email'              => [
                'nullable', 'email', 'max:255',
                Rule::unique('applicants', 'email')->ignore($applicantId),
            ],
            'phone'              => ['nullable', 'string', 'max:20'],
            'mobile'             => ['nullable', 'string', 'max:20'],
            'date_of_birth'      => ['nullable', 'date', 'before:today'],
            'gender'             => ['nullable', 'in:male,female'],
            'civil_status'       => ['nullable', 'in:single,married,widowed,separated,divorced'],
            'number_of_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'nationality'        => ['nullable', 'string', 'max:100'],

            // Physical
            'height_cm'          => ['nullable', 'numeric', 'min:50', 'max:250'],
            'weight_kg'          => ['nullable', 'numeric', 'min:20', 'max:300'],
            'dominant_hand'      => ['nullable', 'in:left,right,both'],
            'blood_type'         => ['nullable', 'in:A,B,AB,O'],

            // Address
            'current_address'    => ['nullable', 'string', 'max:500'],
            'permanent_address'  => ['nullable', 'string', 'max:500'],
            'city'               => ['nullable', 'string', 'max:100'],
            'province'           => ['nullable', 'string', 'max:100'],
            'postal_code'        => ['nullable', 'string', 'max:20'],

            // Passport / IDs
            'passport_number'    => ['nullable', 'string', 'max:50'],
            'passport_expiry'    => ['nullable', 'date'],
            'sss_number'         => ['nullable', 'string', 'max:20'],
            'tin_number'         => ['nullable', 'string', 'max:20'],
            'philhealth_number'  => ['nullable', 'string', 'max:20'],
            'pagibig_number'     => ['nullable', 'string', 'max:20'],

            // Status
            'status'             => ['nullable', 'in:pending,under_review,verified,rejected,incomplete'],
            'quality_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quality_grade'      => ['nullable', 'in:A,B,C,D,F'],

            'assigned_staff_id'  => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}