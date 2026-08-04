<?php

namespace App\Http\Requests\v1\Applicant;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('applicant'));
    }

    public function rules(): array
    {
        $applicantId = $this->route('applicant')?->id;

        return [
            // ─── Personal ────────────────────────────────
            'first_name'         => ['sometimes', 'string', 'max:255'],
            'middle_name'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'last_name'          => ['sometimes', 'string', 'max:255'],
            'suffix'             => ['sometimes', 'nullable', 'string', 'max:10'],
            'email'              => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('applicants', 'email')->ignore($applicantId),
            ],
            'phone'              => ['sometimes', 'nullable', 'string', 'max:20'],
            'mobile'             => ['sometimes', 'nullable', 'string', 'max:20'],
            'date_of_birth'      => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender'             => ['sometimes', 'nullable', 'in:male,female'],
            'civil_status'       => ['sometimes', 'nullable', 'in:single,married,widowed,separated,divorced'],
            'number_of_children' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:20'],
            'nationality'        => ['sometimes', 'nullable', 'string', 'max:100'],

            // ─── Physical ────────────────────────────────
            'height_cm'          => ['sometimes', 'nullable', 'numeric', 'min:50', 'max:250'],
            'weight_kg'          => ['sometimes', 'nullable', 'numeric', 'min:20', 'max:300'],
            'dominant_hand'      => ['sometimes', 'nullable', 'in:left,right,both'],
            'blood_type'         => ['sometimes', 'nullable', 'in:A,B,AB,O'],

            // ─── Address ─────────────────────────────────
            'current_address'    => ['sometimes', 'nullable', 'string', 'max:500'],
            'permanent_address'  => ['sometimes', 'nullable', 'string', 'max:500'],
            'city'               => ['sometimes', 'nullable', 'string', 'max:100'],
            'province'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code'        => ['sometimes', 'nullable', 'string', 'max:20'],

            // ─── Passport / IDs ──────────────────────────
            'passport_number'    => ['sometimes', 'nullable', 'string', 'max:50'],
            'passport_expiry'    => ['sometimes', 'nullable', 'date', 'after:today'],
            'sss_number'         => ['sometimes', 'nullable', 'string', 'max:20'],
            'tin_number'         => ['sometimes', 'nullable', 'string', 'max:20'],
            'philhealth_number'  => ['sometimes', 'nullable', 'string', 'max:20'],
            'pagibig_number'     => ['sometimes', 'nullable', 'string', 'max:20'],

            // ─── Application Status ───────────────────────
            'status'             => [
                'sometimes',
                'string',
                Rule::in([
                    'pending',
                    'under_review',
                    'verified',
                    'incomplete',
                    'final_list',
                    'rejected',
                ]),
            ],
            'rejection_reason'   => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
                // Required if status is being set to rejected
                Rule::requiredIf(fn () => $this->input('status') === 'rejected'),
            ],

            // ─── Quality ─────────────────────────────────
            'quality_score'      => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'quality_grade'      => ['sometimes', 'nullable', 'in:A,B,C,D,F'],

            // ─── Staff ───────────────────────────────────
            'assigned_staff_id'  => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'                   => 'Invalid status. Must be one of: pending, under_review, verified, incomplete, final_list, rejected.',
            'rejection_reason.required_if' => 'A rejection reason is required when rejecting an applicant.',
        ];
    }
}