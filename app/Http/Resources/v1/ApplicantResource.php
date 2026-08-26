<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Safe relation check to prevent N+1 queries during list fetching
        $family = $this->relationLoaded('family') ? $this->family : null;

        return [
            'id'             => $this->id,
            'applicant_code' => $this->applicant_code,

            // ─── AIS / Trade Test ─────────────────────────────────────────
            'applied_position'        => $this->applied_position,
            'trade_test_try'          => $this->trade_test_try,
            'trade_test_date'         => $this->trade_test_date?->format('Y-m-d'),
            'birthplace'              => $this->birthplace,
            'religion'                => $this->religion,
            'english_proficiency_pct' => (int) ($this->english_proficiency_pct ?? 0),

            // ─── Personal ─────────────────────────────────────────────────
            'first_name'         => $this->first_name,
            'middle_name'        => $this->middle_name,
            'last_name'          => $this->last_name,
            'suffix'             => $this->suffix,
            'full_name'          => $this->full_name,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'mobile'             => $this->mobile,
            'date_of_birth'      => $this->date_of_birth?->format('Y-m-d'),
            'age'                => $this->age,
            'gender'             => $this->gender,
            'civil_status'       => $this->civil_status,
            'number_of_children' => $this->number_of_children,
            'nationality'        => $this->nationality,

            // ─── Physical ─────────────────────────────────────────────────
            'height_cm'     => $this->height_cm ? (float) $this->height_cm : null,
            'weight_kg'     => $this->weight_kg ? (float) $this->weight_kg : null,
            'dominant_hand' => $this->dominant_hand,
            'blood_type'    => $this->blood_type,

            // ─── Address ──────────────────────────────────────────────────
            'current_address'   => $this->current_address,
            'permanent_address' => $this->permanent_address,
            'city'              => $this->city,
            'province'          => $this->province,
            'postal_code'       => $this->postal_code,

            // ─── Passport / IDs ───────────────────────────────────────────
            'passport_number'   => $this->passport_number,
            'passport_expiry'   => $this->passport_expiry?->format('Y-m-d'),
            'sss_number'        => $this->sss_number,
            'tin_number'        => $this->tin_number,
            'philhealth_number' => $this->philhealth_number,
            'pagibig_number'    => $this->pagibig_number,

            // ─── Status ───────────────────────────────────────────────────
            'status'           => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'final_listed_at'  => $this->final_listed_at?->toIso8601String(),
            'rejected_at'      => $this->rejected_at?->toIso8601String(),

            // ─── Quality ──────────────────────────────────────────────────
            'quality_score' => (float) $this->quality_score,
            'quality_grade' => $this->quality_grade,

            // ─── Japan Deployment Profile ─────────────────────────────────
            'skill_category'      => $this->skill_category,
            'trade_or_occupation' => $this->trade_or_occupation,

            'language' => [
                'understands_basic_english' => (bool) $this->understands_basic_english,
                'jlpt_level'                => $this->jlpt_level,
            ],

            'deployment' => [
                'willing_to_be_deployed'    => (bool) $this->willing_to_be_deployed,
                'japan_deployment_ready'    => (bool) $this->japan_deployment_ready,
                'preferred_work_location'   => $this->preferred_work_location,
                'previous_japan_experience' => (bool) $this->previous_japan_experience,
                'years_japan_experience'    => (int) $this->years_japan_experience,
                'has_titp_certificate'      => (bool) $this->has_titp_certificate,
                'titp_occupation'           => $this->titp_occupation,
                'ssw_eligible'              => (bool) $this->ssw_eligible,
            ],

            'salary' => [
                'expected_salary' => $this->expected_salary !== null
                    ? (float) $this->expected_salary
                    : null,
                'expected_salary_currency' => $this->expected_salary_currency,
                'current_salary' => $this->current_salary !== null
                    ? (float) $this->current_salary
                    : null,
                'current_salary_currency' => $this->current_salary_currency,
            ],

            // ─── Family Details (ADDED: AIS Family Background Sentences) ───
            'family' => [
                'father' => [
                    'name'       => $family?->father_name ?? $this->father_name,
                    'occupation' => $family?->father_occupation ?? $this->father_occupation,
                    'contact'    => $family?->father_contact ?? $this->father_contact,
                ],
                'mother' => [
                    'name'       => $family?->mother_name ?? $this->mother_name,
                    'occupation' => $family?->mother_occupation ?? $this->mother_occupation,
                    'contact'    => $family?->mother_contact ?? $this->mother_contact,
                ],
                'spouse' => [
                    'name'        => $family?->spouse_name ?? $this->spouse_name,
                    'occupation'  => $family?->spouse_occupation ?? $this->spouse_occupation,
                    'contact'     => $family?->spouse_contact ?? $this->spouse_contact,
                    'salary'      => $family?->spouse_salary !== null ? (float) $family->spouse_salary : null,
                    'salary_unit' => $family?->spouse_salary_unit,
                ],
                'emergency_contact' => [
                    'name'         => $family?->emergency_contact_name ?? $this->emergency_contact_name,
                    'relationship' => $family?->emergency_contact_relationship ?? $this->emergency_contact_relationship,
                    'phone'        => $family?->emergency_contact_phone ?? $this->emergency_contact_phone,
                    'address'      => $family?->emergency_contact_address ?? $this->emergency_contact_address,
                ],
                // 🎯 ADDED FOR AIS FAMILY BACKGROUND TEXT
                'living_situation'     => $family?->living_situation ?? $this->living_situation,
                'birth_order'          => $family?->birth_order ?? $this->birth_order,
                'siblings_count'       => $family?->siblings_count ?? $this->siblings_count,
                'siblings_description' => $family?->siblings_description ?? $this->siblings_description,
            ],

            // ─── Japan Contacts ───────────────────────────────────────────
            'japan_contacts' => $this->whenLoaded(
                'japanContacts',
                fn () => ApplicantJapanContactResource::collection($this->japanContacts)
            ),

            // ─── Staff Relations ──────────────────────────────────────────
            'assigned_staff' => $this->whenLoaded('assignedStaff', fn () => [
                'id'        => $this->assignedStaff->id,
                'full_name' => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
                'name'      => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
            ]),

            'reviewer' => $this->whenLoaded('reviewer', fn () => [
                'id'        => $this->reviewer->id,
                'full_name' => $this->reviewer->full_name ?? $this->reviewer->name,
                'name'      => $this->reviewer->full_name ?? $this->reviewer->name,
            ]),

            'creator' => $this->whenLoaded('creator', fn () => [
                'id'        => $this->creator->id,
                'full_name' => $this->creator->full_name ?? $this->creator->name,
                'name'      => $this->creator->full_name ?? $this->creator->name,
            ]),

            // ─── Sub-models ───────────────────────────────────────────────
            'lifestyle' => $this->whenLoaded(
                'lifestyle',
                fn () => $this->lifestyle
                    ? new ApplicantLifestyleResource($this->lifestyle)
                    : null
            ),

            'educations' => $this->whenLoaded(
                'educations',
                fn () => ApplicantEducationResource::collection($this->educations)
            ),

            'employments' => $this->whenLoaded(
                'employments',
                fn () => ApplicantEmploymentResource::collection($this->employments)
            ),

            'tattoos' => $this->whenLoaded(
                'tattoos',
                fn () => ApplicantTattooResource::collection($this->tattoos)
            ),

            // ─── Documents / Biodata ──────────────────────────────────────
            'documents' => $this->whenLoaded(
                'currentDocuments',
                fn () => ApplicantDocumentResource::collection($this->currentDocuments)
            ),

            'biodata' => $this->whenLoaded('currentDocuments', function () {
                $doc = $this->currentDocuments->first(
                    fn ($d) => strtoupper((string) ($d->documentType?->code ?? '')) === 'BIODATA'
                );

                return $doc ? new ApplicantDocumentResource($doc) : null;
            }),

            // ─── Applicant Batches ────────────────────────────────────────
            'applicant_batches' => $this->whenLoaded(
                'applicantBatches',
                fn () => $this->applicantBatches->map(fn ($ab) => [
                    'id'               => $ab->id,
                    'applicant_id'     => $ab->applicant_id,
                    'batch_id'         => $ab->batch_id,
                    'status'           => $ab->status,
                    'assigned_at'      => $ab->assigned_at?->toIso8601String(),
                    'interview_date'   => $ab->interview_date?->format('Y-m-d'),
                    'medical_date'     => $ab->medical_date?->format('Y-m-d'),
                    'exam_date'        => $ab->exam_date?->format('Y-m-d'),
                    'accepted_at'      => $ab->accepted_at?->toIso8601String(),
                    'deployed_at'      => $ab->deployed_at?->toIso8601String(),
                    'exam_score'       => $ab->exam_score !== null ? (float) $ab->exam_score : null,
                    'interview_notes'  => $ab->interview_notes,
                    'medical_notes'    => $ab->medical_notes,
                    'rejection_reason' => $ab->rejection_reason,
                    'remarks'          => $ab->remarks,

                    'batch' => $ab->relationLoaded('batch') && $ab->batch ? [
                        'id'           => $ab->batch->id,
                        'batch_number' => $ab->batch->batch_number ?? null,
                        'name'         => $ab->batch->name,
                        'country'      => $ab->batch->country,
                        'status'       => $ab->batch->status,
                        'is_active'    => (bool) $ab->batch->is_active,
                    ] : null,

                    'processed_by' => $ab->relationLoaded('processedBy') && $ab->processedBy ? [
                        'id'        => $ab->processedBy->id,
                        'full_name' => $ab->processedBy->full_name ?? $ab->processedBy->name,
                    ] : null,

                    'deployment_country'       => $ab->deployment_country,
                    'deployment_company'       => $ab->deployment_company,
                    'deployment_position'      => $ab->deployment_position,
                    'contract_duration_months' => $ab->contract_duration_months,
                    'contract_start_date'      => $ab->contract_start_date?->format('Y-m-d'),
                    'contract_end_date'        => $ab->contract_end_date?->format('Y-m-d'),
                    'monthly_salary'           => $ab->monthly_salary !== null ? (float) $ab->monthly_salary : null,
                    'salary_currency'          => $ab->salary_currency,
                    'flight_date'              => $ab->flight_date?->format('Y-m-d'),
                    'visa_type'                => $ab->visa_type,
                    'deployment_notes'         => $ab->deployment_notes,
                    'cancellation_reason'      => $ab->cancellation_reason,
                    'cancelled_at'             => $ab->cancelled_at?->toIso8601String(),
                ])
            ),

            // ─── Timestamps ───────────────────────────────────────────────
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}