<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('applicant.update');
    }

    public function rules(): array
    {
        $applicantId = $this->route('applicant')?->id;

        return [
            // ── Personal ──────────────────────────────────────────────────
            'first_name'         => ['sometimes', 'string', 'max:100'],
            'middle_name'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'last_name'          => ['sometimes', 'string', 'max:100'],
            'suffix'             => ['sometimes', 'nullable', 'string', 'max:20'],
            'email'              => [
                'sometimes',
                'email',
                Rule::unique('applicants', 'email')->ignore($applicantId),
            ],
            'phone'              => ['sometimes', 'nullable', 'string', 'max:30'],
            'mobile'             => ['sometimes', 'nullable', 'string', 'max:30'],
            'date_of_birth'      => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender'             => ['sometimes', 'nullable', Rule::in(['male', 'female'])],
            'civil_status'       => ['sometimes', 'nullable', Rule::in([
                'single',
                'married',
                'widowed',
                'separated',
                'divorced',
            ])],
            'number_of_children' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:20'],
            'nationality'        => ['sometimes', 'nullable', 'string', 'max:60'],

            // ── Physical ──────────────────────────────────────────────────
            'height_cm'     => ['sometimes', 'nullable', 'numeric', 'min:50',  'max:250'],
            'weight_kg'     => ['sometimes', 'nullable', 'numeric', 'min:20',  'max:300'],
            'dominant_hand' => ['sometimes', 'nullable', Rule::in(['left', 'right', 'both'])],
            'blood_type'    => ['sometimes', 'nullable', Rule::in(['A', 'B', 'AB', 'O'])],

            // ── Address ───────────────────────────────────────────────────
            'current_address'   => ['sometimes', 'nullable', 'string', 'max:500'],
            'permanent_address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'city'              => ['sometimes', 'nullable', 'string', 'max:100'],
            'province'          => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code'       => ['sometimes', 'nullable', 'string', 'max:20'],

            // ── Passport / IDs ────────────────────────────────────────────
            'passport_number'  => ['sometimes', 'nullable', 'string', 'max:50'],
            'passport_expiry'  => ['sometimes', 'nullable', 'date'],
            'sss_number'       => ['sometimes', 'nullable', 'string', 'max:50'],
            'tin_number'       => ['sometimes', 'nullable', 'string', 'max:50'],
            'philhealth_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'pagibig_number'   => ['sometimes', 'nullable', 'string', 'max:50'],

            // ── Skill / Trade (Phase 1) ───────────────────────────────────
            'skill_category'      => ['sometimes', 'nullable', Rule::in([
                'skilled',
                'semi_skilled',
                'unskilled',
            ])],
            'trade_or_occupation' => ['sometimes', 'nullable', 'string', 'max:100'],

            // ── Language (Phase 1) ────────────────────────────────────────
            'understands_basic_english' => ['sometimes', 'boolean'],
            'jlpt_level'                => ['sometimes', 'nullable', Rule::in([
                'N5',
                'N4',
                'N3',
                'N2',
                'N1',
            ])],

            // ── Japan Deployment (Phase 1) ────────────────────────────────
            'willing_to_be_deployed'  => ['sometimes', 'boolean'],
            'japan_deployment_ready'  => ['sometimes', 'boolean'],
            'preferred_work_location' => ['sometimes', 'nullable', 'string', 'max:100'],

            // ── Japan Experience (Phase 1) ────────────────────────────────
            'previous_japan_experience' => ['sometimes', 'boolean'],
            'years_japan_experience'    => ['sometimes', 'nullable', 'integer', 'min:0', 'max:50'],

            // ── Certifications (Phase 1) ──────────────────────────────────
            'has_titp_certificate' => ['sometimes', 'boolean'],
            'titp_occupation'      => ['sometimes', 'nullable', 'string', 'max:100'],
            'ssw_eligible'         => ['sometimes', 'boolean'],

            // ── Salary (Phase 1) ──────────────────────────────────────────
            'expected_salary'          => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'expected_salary_currency' => ['sometimes', 'nullable', 'string', 'size:3'],
            'current_salary'           => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'current_salary_currency'  => ['sometimes', 'nullable', 'string', 'size:3'],

            // ── Family (Phase 1) ──────────────────────────────────────────
            'father_name'       => ['sometimes', 'nullable', 'string', 'max:150'],
            'father_occupation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'father_contact'    => ['sometimes', 'nullable', 'string', 'max:30'],
            'mother_name'       => ['sometimes', 'nullable', 'string', 'max:150'],
            'mother_occupation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'mother_contact'    => ['sometimes', 'nullable', 'string', 'max:30'],
            'spouse_name'       => ['sometimes', 'nullable', 'string', 'max:150'],
            'spouse_occupation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'spouse_contact'    => ['sometimes', 'nullable', 'string', 'max:30'],

            // ── Emergency Contact (Phase 1) ───────────────────────────────
            'emergency_contact_name'         => ['sometimes', 'nullable', 'string', 'max:150'],
            'emergency_contact_relationship' => ['sometimes', 'nullable', 'string', 'max:60'],
            'emergency_contact_phone'        => ['sometimes', 'nullable', 'string', 'max:30'],
            'emergency_contact_address'      => ['sometimes', 'nullable', 'string', 'max:500'],

            // ── Status ────────────────────────────────────────────────────
            'status'           => ['sometimes', Rule::in([
                'pending',
                'under_review',
                'verified',
                'incomplete',
                'final_list',
                'rejected',
            ])],
            'rejection_reason' => ['sometimes', 'nullable', 'string', 'max:1000'],

            // ── Quality ───────────────────────────────────────────────────
            'quality_score' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'quality_grade' => ['sometimes', 'nullable', 'string', 'max:2'],

            // ── Staff ─────────────────────────────────────────────────────
            'assigned_staff_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->coerceBooleans([
            'understands_basic_english',
            'willing_to_be_deployed',
            'japan_deployment_ready',
            'previous_japan_experience',
            'has_titp_certificate',
            'ssw_eligible',
        ]);
    }

    private function coerceBooleans(array $fields): void
    {
        $patch = [];
        foreach ($fields as $field) {
            if ($this->has($field)) {
                $patch[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOLEAN,
                );
            }
        }
        $this->merge($patch);
    }
}
