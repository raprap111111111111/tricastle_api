<?php

namespace App\Http\Requests\v1\Portal;

use Illuminate\Foundation\Http\FormRequest;

class SubmitProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Core Personal & Contact ────────────────────────────────────
            'mobile'        => 'sometimes|required|string|max:30',
            'phone'         => 'nullable|string|max:30',
            'facebook_url'  => 'nullable|string|max:255',
            'date_of_birth' => 'sometimes|required|date',
            'gender'        => 'nullable|string|in:male,female',
            'civil_status'  => 'nullable|string|max:50',
            'religion'      => 'nullable|string|max:50',
            'birthplace'    => 'nullable|string|max:150',
            'nationality'   => 'nullable|string|max:100',
            'number_of_children' => 'nullable|integer|min:0|max:20',

            // ── Address ────────────────────────────────────────────────────
            'current_address'   => 'sometimes|required|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'city'              => 'sometimes|required|string|max:100',
            'province'          => 'sometimes|required|string|max:100',
            'postal_code'       => 'nullable|string|max:20',

            // ── Physical Characteristics ──────────────────────────────────
            'height_cm'     => 'nullable|numeric|between:50,250',
            'weight_kg'     => 'nullable|numeric|between:20,300',
            'dominant_hand' => 'nullable|string|in:left,right,both',
            'blood_type'    => 'nullable|string|max:10',

            // ── Passport & Government IDs (Nullable: Optional for applicants)
            'passport_number'     => 'nullable|string|max:50',
            'passport_expiry'     => 'nullable|date',
            'passport_issue_date' => 'nullable|date',
            'sss_number'          => 'nullable|string|max:50',
            'tin_number'          => 'nullable|string|max:50',
            'philhealth_number'   => 'nullable|string|max:50',
            'pagibig_number'      => 'nullable|string|max:50',

            // ── Deployment & Skills ─────────────────────────────────────────
            'trade_or_occupation'       => 'nullable|string|max:100',
            'skill_category'            => 'nullable|string|max:100',
            'jlpt_level'                => 'nullable|string|max:10',
            'understands_basic_english' => 'nullable|boolean',
            'willing_to_be_deployed'    => 'nullable|boolean',
            'japan_deployment_ready'    => 'nullable|boolean',
            'previous_japan_experience' => 'nullable|boolean',
            'years_japan_experience'    => 'nullable|integer|min:0|max:50',
            'has_titp_certificate'      => 'nullable|boolean',
            'titp_occupation'           => 'nullable|string|max:100',
            'ssw_eligible'              => 'nullable|boolean',

            // ── Family & Emergency Contacts ────────────────────────────────
            'father_name'                     => 'nullable|string|max:150',
            'mother_name'                     => 'nullable|string|max:150',
            'spouse_name'                     => 'nullable|string|max:150',
            'emergency_contact_name'         => 'nullable|string|max:150',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_phone'        => 'nullable|string|max:30',
            'emergency_contact_address'      => 'nullable|string|max:255',
        ];
    }
}