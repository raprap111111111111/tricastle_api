<?php

namespace App\Http\Requests\v1\Portal;

use App\Enums\CivilStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicRegisterApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize inputs: Convert empty strings to null & cast numeric/boolean types
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Core & Personal
            'middle_name'                    => $this->filled('middle_name') ? trim($this->input('middle_name')) : null,
            'suffix'                         => $this->filled('suffix') ? trim($this->input('suffix')) : null,
            'birthplace'                     => $this->filled('birthplace') ? trim($this->input('birthplace')) : null,
            'religion'                       => $this->filled('religion') ? trim($this->input('religion')) : null,
            'nationality'                    => $this->filled('nationality') ? trim($this->input('nationality')) : 'Filipino',
            'gender'                         => $this->filled('gender') ? $this->input('gender') : null,
            'civil_status'                   => $this->filled('civil_status') ? $this->input('civil_status') : null,
            'number_of_children'             => $this->filled('number_of_children') ? (int) $this->input('number_of_children') : 0,
            'phone'                          => $this->filled('phone') ? trim($this->input('phone')) : null,
            'mobile'                         => $this->filled('mobile') ? trim($this->input('mobile')) : null,
            'facebook_url'                   => $this->filled('facebook_url') ? trim($this->input('facebook_url')) : null,

            // Physical
            'height_cm'                      => $this->filled('height_cm') ? (float) $this->input('height_cm') : null,
            'weight_kg'                      => $this->filled('weight_kg') ? (float) $this->input('weight_kg') : null,
            'dominant_hand'                  => $this->filled('dominant_hand') ? $this->input('dominant_hand') : null,
            'blood_type'                     => $this->filled('blood_type') ? $this->input('blood_type') : null,

            // Address
            'current_address'                => $this->filled('current_address') ? trim($this->input('current_address')) : null,
            'permanent_address'              => $this->filled('permanent_address') ? trim($this->input('permanent_address')) : null,
            'city'                           => $this->filled('city') ? trim($this->input('city')) : null,
            'province'                       => $this->filled('province') ? trim($this->input('province')) : null,
            'postal_code'                    => $this->filled('postal_code') ? trim($this->input('postal_code')) : null,

            // Passport & IDs
            'passport_number'                => $this->filled('passport_number') ? strtoupper(trim($this->input('passport_number'))) : null,
            'passport_expiry'                => $this->filled('passport_expiry') ? $this->input('passport_expiry') : null,
            'sss_number'                     => $this->filled('sss_number') ? trim($this->input('sss_number')) : null,
            'tin_number'                     => $this->filled('tin_number') ? trim($this->input('tin_number')) : null,
            'philhealth_number'              => $this->filled('philhealth_number') ? trim($this->input('philhealth_number')) : null,
            'pagibig_number'                 => $this->filled('pagibig_number') ? trim($this->input('pagibig_number')) : null,

            // Position & Deployment
            'applied_position'               => $this->filled('applied_position') ? trim($this->input('applied_position')) : null,
            'skill_category'                 => $this->filled('skill_category') ? $this->input('skill_category') : null,
            'trade_or_occupation'            => $this->filled('trade_or_occupation') ? trim($this->input('trade_or_occupation')) : null,
            'understands_basic_english'      => filter_var($this->input('understands_basic_english', true), FILTER_VALIDATE_BOOLEAN),
            'jlpt_level'                     => $this->filled('jlpt_level') ? $this->input('jlpt_level') : null,
            'willing_to_be_deployed'         => filter_var($this->input('willing_to_be_deployed', true), FILTER_VALIDATE_BOOLEAN),
            'japan_deployment_ready'         => filter_var($this->input('japan_deployment_ready', false), FILTER_VALIDATE_BOOLEAN),
            'preferred_work_location'        => $this->filled('preferred_work_location') ? trim($this->input('preferred_work_location')) : null,
            'previous_japan_experience'      => filter_var($this->input('previous_japan_experience', false), FILTER_VALIDATE_BOOLEAN),
            'years_japan_experience'         => $this->filled('years_japan_experience') ? (int) $this->input('years_japan_experience') : 0,
            'has_titp_certificate'           => filter_var($this->input('has_titp_certificate', false), FILTER_VALIDATE_BOOLEAN),
            'titp_occupation'                => $this->filled('titp_occupation') ? trim($this->input('titp_occupation')) : null,
            'ssw_eligible'                   => filter_var($this->input('ssw_eligible', false), FILTER_VALIDATE_BOOLEAN),
            'expected_salary'                => $this->filled('expected_salary') ? (float) $this->input('expected_salary') : null,
            'expected_salary_currency'       => $this->filled('expected_salary_currency') ? trim($this->input('expected_salary_currency')) : 'JPY',

            // Family
            'father_name'                    => $this->filled('father_name') ? trim($this->input('father_name')) : null,
            'father_occupation'              => $this->filled('father_occupation') ? trim($this->input('father_occupation')) : null,
            'father_contact'                 => $this->filled('father_contact') ? trim($this->input('father_contact')) : null,
            'mother_name'                    => $this->filled('mother_name') ? trim($this->input('mother_name')) : null,
            'mother_occupation'              => $this->filled('mother_occupation') ? trim($this->input('mother_occupation')) : null,
            'mother_contact'                 => $this->filled('mother_contact') ? trim($this->input('mother_contact')) : null,
            'spouse_name'                    => $this->filled('spouse_name') ? trim($this->input('spouse_name')) : null,
            'spouse_occupation'              => $this->filled('spouse_occupation') ? trim($this->input('spouse_occupation')) : null,
            'spouse_contact'                 => $this->filled('spouse_contact') ? trim($this->input('spouse_contact')) : null,

            // Emergency Contact
            'emergency_contact_name'         => $this->filled('emergency_contact_name') ? trim($this->input('emergency_contact_name')) : null,
            'emergency_contact_relationship' => $this->filled('emergency_contact_relationship') ? trim($this->input('emergency_contact_relationship')) : null,
            'emergency_contact_phone'        => $this->filled('emergency_contact_phone') ? trim($this->input('emergency_contact_phone')) : null,
            'emergency_contact_address'      => $this->filled('emergency_contact_address') ? trim($this->input('emergency_contact_address')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            // Core Identity & Credentials
            'first_name'                 => ['required', 'string', 'max:100'],
            'last_name'                  => ['required', 'string', 'max:100'],
            'middle_name'                => ['nullable', 'string', 'max:100'],
            'suffix'                     => ['nullable', 'string', 'max:20'],
            'date_of_birth'              => ['required', 'date'],
            'email'                      => ['required', 'email', 'max:255', 'unique:applicants,email'],
            'password'                   => ['required', 'string', 'min:8', 'confirmed'],

            // Personal
            'gender'                     => ['nullable', 'string', 'in:male,female'],
            'civil_status'               => ['nullable', Rule::enum(CivilStatus::class)],
            'religion'                   => ['nullable', 'string', 'max:100'],
            'birthplace'                 => ['nullable', 'string', 'max:150'],
            'nationality'                => ['nullable', 'string', 'max:100'],
            'number_of_children'         => ['nullable', 'integer', 'min:0'],
            'phone'                      => ['nullable', 'string', 'max:30'],
            'mobile'                     => ['required', 'string', 'max:30'],
            'facebook_url'               => ['nullable', 'string', 'max:255'],

            // Physical
            'height_cm'                  => ['nullable', 'numeric', 'min:50', 'max:250'],
            'weight_kg'                  => ['nullable', 'numeric', 'min:20', 'max:250'],
            'dominant_hand'              => ['nullable', 'string', 'in:left,right,both'],
            'blood_type'                 => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],

            // Address (PSGC)
            'current_address'            => ['required', 'string', 'max:255'],
            'permanent_address'          => ['nullable', 'string', 'max:255'],
            'city'                       => ['required', 'string', 'max:100'],
            'province'                   => ['required', 'string', 'max:100'],
            'postal_code'                => ['nullable', 'string', 'max:20'],

            // Passport & IDs
            'passport_number'            => ['nullable', 'string', 'max:50'],
            'passport_expiry'            => ['nullable', 'date'],
            'sss_number'                 => ['nullable', 'string', 'max:50'],
            'tin_number'                 => ['nullable', 'string', 'max:50'],
            'philhealth_number'          => ['nullable', 'string', 'max:50'],
            'pagibig_number'             => ['nullable', 'string', 'max:50'],

            // Skills & Deployment
            'applied_position'           => ['required', 'string', 'max:150'],
            'skill_category'             => ['nullable', 'string', 'in:skilled,semi_skilled,unskilled'],
            'trade_or_occupation'        => ['nullable', 'string', 'max:150'],
            'jlpt_level'                 => ['nullable', 'string', 'in:N1,N2,N3,N4,N5'],
            'understands_basic_english'   => ['nullable', 'boolean'],
            'willing_to_be_deployed'     => ['nullable', 'boolean'],
            'japan_deployment_ready'     => ['nullable', 'boolean'],
            'preferred_work_location'    => ['nullable', 'string', 'max:150'],
            'previous_japan_experience'  => ['nullable', 'boolean'],
            'years_japan_experience'     => ['nullable', 'integer', 'min:0'],
            'has_titp_certificate'        => ['nullable', 'boolean'],
            'titp_occupation'            => ['nullable', 'string', 'max:150'],
            'ssw_eligible'                => ['nullable', 'boolean'],
            'expected_salary'            => ['nullable', 'numeric', 'min:0'],
            'expected_salary_currency'   => ['nullable', 'string', 'max:10'],

            // Family
            'father_name'                => ['nullable', 'string', 'max:150'],
            'father_occupation'          => ['nullable', 'string', 'max:100'],
            'father_contact'             => ['nullable', 'string', 'max:30'],
            'mother_name'                => ['nullable', 'string', 'max:150'],
            'mother_occupation'          => ['nullable', 'string', 'max:100'],
            'mother_contact'             => ['nullable', 'string', 'max:30'],
            'spouse_name'                => ['nullable', 'string', 'max:150'],
            'spouse_occupation'          => ['nullable', 'string', 'max:100'],
            'spouse_contact'             => ['nullable', 'string', 'max:30'],

            // Emergency Contact
            'emergency_contact_name'         => ['required', 'string', 'max:150'],
            'emergency_contact_relationship' => ['required', 'string', 'max:50'],
            'emergency_contact_phone'        => ['required', 'string', 'max:30'],
            'emergency_contact_address'      => ['nullable', 'string', 'max:255'],
        ];
    }
}