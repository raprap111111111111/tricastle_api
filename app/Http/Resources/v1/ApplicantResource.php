<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 🎯 Detect if this request is for full profile detail view (e.g., GET /applicants/{id})
        $isDetailView = $request->routeIs('*.show') || $request->boolean('full_profile');

        // 🎯 Find ID Photo document (Lightweight)
        $docs = $this->relationLoaded('currentDocuments')
            ? $this->currentDocuments
            : ($this->relationLoaded('documents') ? $this->documents : collect());

        $photoDoc = $docs->first(function ($d) {
            $code = strtoupper((string) ($d->documentType?->code ?? $d->code ?? ''));
            $name = strtoupper((string) ($d->documentType?->name ?? $d->name ?? ''));
            return str_contains($code, 'PHOTO') || str_contains($code, '2X2') || str_contains($name, 'PHOTO') || str_contains($name, '2X2');
        });

        $streamPhotoUrl = $photoDoc ? url("/api/v1/applicant-documents/{$photoDoc->id}/preview") : null;

        if (
            $streamPhotoUrl &&
            str_starts_with($streamPhotoUrl, 'http://') &&
            !str_contains($streamPhotoUrl, 'localhost') &&
            !str_contains($streamPhotoUrl, '127.0.0.1')
        ) {
            $streamPhotoUrl = str_replace('http://', 'https://', $streamPhotoUrl);
        }

        $legacyPhotoUrl = $this->photo_url ?? $this->profile_photo_url ?? null;
        if ($legacyPhotoUrl && str_contains($legacyPhotoUrl, '/storage/')) {
            $legacyPhotoUrl = null;
        }

        // ═════════════════════════════════════════════════════════════════
        // ⚡ BASE FIELDS (Always included for Table & List views)
        // ═════════════════════════════════════════════════════════════════
        $data = [
            'id'             => $this->id,
            'applicant_code' => $this->applicant_code,

            // Applicant Column & Hover Preview
            'first_name'       => $this->first_name,
            'middle_name'      => $this->middle_name,
            'last_name'        => $this->last_name,
            'suffix'           => $this->suffix,
            'full_name'        => $this->full_name,
            'email'            => $this->email,
            'applied_position' => $this->applied_position,
            'age'              => $this->age,
            'photo_url'        => $streamPhotoUrl ?? $legacyPhotoUrl,
            'profile_photo_url' => $streamPhotoUrl ?? $legacyPhotoUrl,

            // Table Columns
            'nationality'     => $this->nationality,
            'status'          => $this->status,
            'quality_score'   => (float) $this->quality_score,
            'quality_grade'   => $this->quality_grade,
            'passport_expiry' => $this->passport_expiry?->format('Y-m-d'),

            // Deployment Column & Deployments Popover
            'applicant_batches' => $this->whenLoaded(
                'applicantBatches',
                fn() => $this->applicantBatches->map(fn($ab) => [
                    'id'                       => $ab->id,
                    'applicant_id'             => $ab->applicant_id,
                    'batch_id'                 => $ab->batch_id,
                    'status'                   => $ab->status,
                    'assigned_at'              => $ab->assigned_at?->toIso8601String(),
                    'deployed_at'              => $ab->deployed_at?->toIso8601String(),
                    'deployment_country'       => $ab->deployment_country,
                    'deployment_company'       => $ab->deployment_company,
                    'deployment_position'      => $ab->deployment_position,
                    'monthly_salary'           => $ab->monthly_salary,
                    'salary_currency'          => $ab->salary_currency,
                    'contract_duration_months' => $ab->contract_duration_months,
                    'cancelled_at'             => $ab->cancelled_at?->toIso8601String(),
                    'cancellation_reason'      => $ab->cancellation_reason,
                    'batch' => $ab->relationLoaded('batch') && $ab->batch ? [
                        'id'           => $ab->batch->id,
                        'batch_number' => $ab->batch->batch_number ?? null,
                        'name'         => $ab->batch->name,
                        'country'      => $ab->batch->country,
                        'status'       => $ab->batch->status,
                        'is_active'    => (bool) $ab->batch->is_active,
                    ] : null,
                ])
            ),
        ];

        // ═════════════════════════════════════════════════════════════════
        // 🔍 FULL PROFILE FIELDS (Included ONLY on GET /applicants/{id})
        // ═════════════════════════════════════════════════════════════════
        if ($isDetailView) {
            $family = $this->relationLoaded('family') ? $this->family : null;

            $data = array_merge($data, [
                'trade_test_try'          => $this->trade_test_try,
                'trade_test_date'         => $this->trade_test_date?->format('Y-m-d'),
                'birthplace'              => $this->birthplace,
                'religion'                => $this->religion,
                'english_proficiency_pct' => (int) ($this->english_proficiency_pct ?? 0),

                'phone'              => $this->phone,
                'mobile'             => $this->mobile,
                'date_of_birth'      => $this->date_of_birth?->format('Y-m-d'),
                'gender'             => $this->gender,
                'civil_status'       => $this->civil_status,
                'number_of_children' => $this->number_of_children,

                'height_cm'     => $this->height_cm ? (float) $this->height_cm : null,
                'weight_kg'     => $this->weight_kg ? (float) $this->weight_kg : null,
                'dominant_hand' => $this->dominant_hand,
                'blood_type'    => $this->blood_type,

                'current_address'   => $this->current_address,
                'permanent_address' => $this->permanent_address,
                'city'              => $this->city,
                'province'          => $this->province,
                'postal_code'       => $this->postal_code,

                'passport_number'   => $this->passport_number,
                'sss_number'        => $this->sss_number,
                'tin_number'        => $this->tin_number,
                'philhealth_number' => $this->philhealth_number,
                'pagibig_number'    => $this->pagibig_number,

                'rejection_reason' => $this->rejection_reason,
                'final_listed_at'  => $this->final_listed_at?->toIso8601String(),
                'rejected_at'      => $this->rejected_at?->toIso8601String(),

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
                    'expected_salary'          => $this->expected_salary !== null ? (float) $this->expected_salary : null,
                    'expected_salary_currency' => $this->expected_salary_currency,
                    'current_salary'           => $this->current_salary !== null ? (float) $this->current_salary : null,
                    'current_salary_currency'  => $this->current_salary_currency,
                ],

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
                    'living_situation'     => $family?->living_situation ?? $this->living_situation,
                    'birth_order'          => $family?->birth_order ?? $this->birth_order,
                    'siblings_count'       => $family?->siblings_count ?? $this->siblings_count,
                    'siblings_description' => $family?->siblings_description ?? $this->siblings_description,
                ],

                'japan_contacts' => $this->whenLoaded(
                    'japanContacts',
                    fn() => ApplicantJapanContactResource::collection($this->japanContacts)
                ),

                'assigned_staff' => $this->whenLoaded('assignedStaff', fn() => [
                    'id'        => $this->assignedStaff->id,
                    'full_name' => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
                    'name'      => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
                ]),

                'reviewer' => $this->whenLoaded('reviewer', fn() => [
                    'id'        => $this->reviewer->id,
                    'full_name' => $this->reviewer->full_name ?? $this->reviewer->name,
                    'name'      => $this->reviewer->full_name ?? $this->reviewer->name,
                ]),

                'creator' => $this->whenLoaded('creator', fn() => [
                    'id'        => $this->creator->id,
                    'full_name' => $this->creator->full_name ?? $this->creator->name,
                    'name'      => $this->creator->full_name ?? $this->creator->name,
                ]),

                'lifestyle' => $this->whenLoaded(
                    'lifestyle',
                    fn() => $this->lifestyle ? new ApplicantLifestyleResource($this->lifestyle) : null
                ),

                'educations' => $this->whenLoaded(
                    'educations',
                    fn() => ApplicantEducationResource::collection($this->educations)
                ),

                'employments' => $this->whenLoaded(
                    'employments',
                    fn() => ApplicantEmploymentResource::collection($this->employments)
                ),

                'tattoos' => $this->whenLoaded(
                    'tattoos',
                    fn() => ApplicantTattooResource::collection($this->tattoos)
                ),

                'documents' => $this->whenLoaded(
                    'currentDocuments',
                    fn() => ApplicantDocumentResource::collection($this->currentDocuments)
                ),

                'biodata' => $this->whenLoaded('currentDocuments', function () {
                    $doc = $this->currentDocuments->first(
                        fn($d) => strtoupper((string) ($d->documentType?->code ?? '')) === 'BIODATA'
                    );
                    return $doc ? new ApplicantDocumentResource($doc) : null;
                }),

                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ]);
        }

        return $data;
    }
}
