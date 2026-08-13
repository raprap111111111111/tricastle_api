<?php

namespace App\Http\Requests\v1\Applicant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('applicant.create');
    }

    public function rules(): array
    {
        return [
            // ── Personal ──────────────────────────────────────────────────
            'first_name'         => ['required', 'string', 'max:100'],
            'middle_name'        => ['nullable', 'string', 'max:100'],
            'last_name'          => ['required', 'string', 'max:100'],
            'suffix'             => ['nullable', 'string', 'max:20'],
            'email'              => ['required', 'email', 'unique:applicants,email'],
            'phone'              => ['nullable', 'string', 'max:30'],
            'mobile'             => ['nullable', 'string', 'max:30'],
            'date_of_birth'      => ['nullable', 'date', 'before:today'],
            'gender'             => ['nullable', Rule::in(['male', 'female'])],
            'civil_status'       => ['nullable', Rule::in([
                'single', 'married', 'widowed', 'separated', 'divorced',
            ])],
            'number_of_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'nationality'        => ['nullable', 'string', 'max:60'],

            // ── Physical ──────────────────────────────────────────────────
            'height_cm'    => ['nullable', 'numeric', 'min:50',  'max:250'],
            'weight_kg'    => ['nullable', 'numeric', 'min:20',  'max:300'],
            'dominant_hand'=> ['nullable', Rule::in(['left', 'right', 'both'])],
            'blood_type'   => ['nullable', Rule::in(['A', 'B', 'AB', 'O'])],

            // ── Address ───────────────────────────────────────────────────
            'current_address'   => ['nullable', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'city'              => ['nullable', 'string', 'max:100'],
            'province'          => ['nullable', 'string', 'max:100'],
            'postal_code'       => ['nullable', 'string', 'max:20'],

            // ── Passport / IDs ────────────────────────────────────────────
            'passport_number'  => ['nullable', 'string', 'max:50'],
            'passport_expiry'  => ['nullable', 'date', 'after:today'],
            'sss_number'       => ['nullable', 'string', 'max:50'],
            'tin_number'       => ['nullable', 'string', 'max:50'],
            'philhealth_number'=> ['nullable', 'string', 'max:50'],
            'pagibig_number'   => ['nullable', 'string', 'max:50'],

            // ── Skill / Trade (Phase 1) ───────────────────────────────────
            'skill_category'     => ['nullable', Rule::in([
                'skilled', 'semi_skilled', 'unskilled',
            ])],
            'trade_or_occupation'=> ['nullable', 'string', 'max:100'],

            // ── Language (Phase 1) ────────────────────────────────────────
            'understands_basic_english' => ['nullable', 'boolean'],
            'jlpt_level'                => ['nullable', Rule::in([
                'N5', 'N4', 'N3', 'N2', 'N1',
            ])],

            // ── Japan Deployment (Phase 1) ────────────────────────────────
            'willing_to_be_deployed'  => ['nullable', 'boolean'],
            'japan_deployment_ready'  => ['nullable', 'boolean'],
            'preferred_work_location' => ['nullable', 'string', 'max:100'],

            // ── Japan Experience (Phase 1) ────────────────────────────────
            'previous_japan_experience' => ['nullable', 'boolean'],
            'years_japan_experience'    => [
                'nullable', 'integer', 'min:0', 'max:50',
                'required_if:previous_japan_experience,true',
            ],

            // ── Certifications (Phase 1) ──────────────────────────────────
            'has_titp_certificate' => ['nullable', 'boolean'],
            'titp_occupation'      => [
                'nullable', 'string', 'max:100',
                'required_if:has_titp_certificate,true',
            ],
            'ssw_eligible' => ['nullable', 'boolean'],

            // ── Salary (Phase 1) ──────────────────────────────────────────
            'expected_salary'          => ['nullable', 'numeric', 'min:0'],
            'expected_salary_currency' => ['nullable', 'string', 'size:3'],
            'current_salary'           => ['nullable', 'numeric', 'min:0'],
            'current_salary_currency'  => ['nullable', 'string', 'size:3'],

            // ── Family (Phase 1) ──────────────────────────────────────────
            'father_name'       => ['nullable', 'string', 'max:150'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_contact'    => ['nullable', 'string', 'max:30'],
            'mother_name'       => ['nullable', 'string', 'max:150'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_contact'    => ['nullable', 'string', 'max:30'],
            'spouse_name'       => ['nullable', 'string', 'max:150'],
            'spouse_occupation' => ['nullable', 'string', 'max:100'],
            'spouse_contact'    => ['nullable', 'string', 'max:30'],

            // ── Emergency Contact (Phase 1) ───────────────────────────────
            'emergency_contact_name'         => ['nullable', 'string', 'max:150'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:60'],
            'emergency_contact_phone'        => ['nullable', 'string', 'max:30'],
            'emergency_contact_address'      => ['nullable', 'string', 'max:500'],

            // ── Staff ─────────────────────────────────────────────────────
            'assigned_staff_id' => ['nullable', 'integer', 'exists:users,id'],
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