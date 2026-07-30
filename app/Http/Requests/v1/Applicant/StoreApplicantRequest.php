<?php

namespace App\Http\Requests\v1\Applicant;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Applicant::class);
    }

    public function rules(): array
    {
        return [
            // ─── Personal ────────────────────────────────
            'first_name'         => ['required', 'string', 'max:255'],
            'middle_name'        => ['nullable', 'string', 'max:255'],
            'last_name'          => ['required', 'string', 'max:255'],
            'suffix'             => ['nullable', 'string', 'max:10'],
            'email'              => ['required', 'email', 'max:255', 'unique:applicants,email'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'mobile'             => ['nullable', 'string', 'max:20'],
            'date_of_birth'      => ['nullable', 'date', 'before:today'],
            'gender'             => ['nullable', 'in:male,female'],
            'civil_status'       => ['nullable', 'in:single,married,widowed,separated,divorced'],
            'number_of_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'nationality'        => ['nullable', 'string', 'max:100'],

            // ─── Physical ────────────────────────────────
            'height_cm'          => ['nullable', 'numeric', 'min:50', 'max:250'],
            'weight_kg'          => ['nullable', 'numeric', 'min:20', 'max:300'],
            'dominant_hand'      => ['nullable', 'in:left,right,both'],
            'blood_type'         => ['nullable', 'in:A,B,AB,O'],

            // ─── Address ─────────────────────────────────
            'current_address'    => ['nullable', 'string', 'max:500'],
            'permanent_address'  => ['nullable', 'string', 'max:500'],
            'city'               => ['nullable', 'string', 'max:100'],
            'province'           => ['nullable', 'string', 'max:100'],
            'postal_code'        => ['nullable', 'string', 'max:20'],

            // ─── Passport / IDs ──────────────────────────
            'passport_number'    => ['nullable', 'string', 'max:50'],
            'passport_expiry'    => ['nullable', 'date', 'after:today'],
            'sss_number'         => ['nullable', 'string', 'max:20'],
            'tin_number'         => ['nullable', 'string', 'max:20'],
            'philhealth_number'  => ['nullable', 'string', 'max:20'],
            'pagibig_number'     => ['nullable', 'string', 'max:20'],

            // ─── Staff ───────────────────────────────────
            'assigned_staff_id'  => ['nullable', 'integer', 'exists:users,id'],

            // ─── Batch Assignment (optional) ─────────────
            'batch_id'           => ['nullable', 'integer', 'exists:batches,id'],
            'batch_status'       => [
                'nullable',
                'required_with:batch_id',   // required if batch_id is present
                'in:applied,shortlisted,interview_scheduled,interview_passed,
                    interview_failed,medical_pending,medical_passed,medical_failed,
                    exam_pending,exam_passed,exam_failed,accepted,rejected,
                    withdrawn,deployed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'batch_id.exists'           => 'The selected batch does not exist.',
            'batch_status.required_with' => 'A status is required when assigning a batch.',
            'batch_status.in'           => 'Invalid batch status provided.',
        ];
    }
}