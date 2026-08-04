<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'applicant_code'     => $this->applicant_code,

            // ─── Personal ────────────────────────────────
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

            // ─── Physical ────────────────────────────────
            'height_cm'          => $this->height_cm ? (float) $this->height_cm : null,
            'weight_kg'          => $this->weight_kg ? (float) $this->weight_kg : null,
            'dominant_hand'      => $this->dominant_hand,
            'blood_type'         => $this->blood_type,

            // ─── Address ─────────────────────────────────
            'current_address'    => $this->current_address,
            'permanent_address'  => $this->permanent_address,
            'city'               => $this->city,
            'province'           => $this->province,
            'postal_code'        => $this->postal_code,

            // ─── Passport / IDs ──────────────────────────
            'passport_number'    => $this->passport_number,
            'passport_expiry'    => $this->passport_expiry?->format('Y-m-d'),
            'sss_number'         => $this->sss_number,
            'tin_number'         => $this->tin_number,
            'philhealth_number'  => $this->philhealth_number,
            'pagibig_number'     => $this->pagibig_number,

            // ─── Status ──────────────────────────────────
            'status'             => $this->status,
            'rejection_reason'   => $this->rejection_reason,
            'final_listed_at'    => $this->final_listed_at?->toIso8601String(),
            'rejected_at'        => $this->rejected_at?->toIso8601String(),

            // ─── Quality ─────────────────────────────────
            'quality_score'      => (float) $this->quality_score,
            'quality_grade'      => $this->quality_grade,

            // ─── Staff Relations ─────────────────────────
            'assigned_staff'     => $this->whenLoaded('assignedStaff', fn () => [
                'id'        => $this->assignedStaff->id,
                'full_name' => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
                'name'      => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
            ]),

            'reviewer'           => $this->whenLoaded('reviewer', fn () => [
                'id'        => $this->reviewer->id,
                'full_name' => $this->reviewer->full_name ?? $this->reviewer->name,
                'name'      => $this->reviewer->full_name ?? $this->reviewer->name,
            ]),

            'creator'            => $this->whenLoaded('creator', fn () => [
                'id'        => $this->creator->id,
                'full_name' => $this->creator->full_name ?? $this->creator->name,
                'name'      => $this->creator->full_name ?? $this->creator->name,
            ]),

            // ─── Sub-models ──────────────────────────────
            'lifestyle'          => $this->whenLoaded('lifestyle', fn () =>
                $this->lifestyle ? new ApplicantLifestyleResource($this->lifestyle) : null
            ),

            'educations'         => $this->whenLoaded('educations', fn () =>
                ApplicantEducationResource::collection($this->educations)
            ),

            'employments'        => $this->whenLoaded('employments', fn () =>
                ApplicantEmploymentResource::collection($this->employments)
            ),

            'tattoos'            => $this->whenLoaded('tattoos', fn () =>
                ApplicantTattooResource::collection($this->tattoos)
            ),

            // ─── Batches (direct HasMany) ────────────────
            'applicant_batches'  => $this->whenLoaded('applicantBatches', fn () =>
                $this->applicantBatches->map(fn ($ab) => [
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
                    'batch'            => $ab->relationLoaded('batch') && $ab->batch ? [
                        'id'           => $ab->batch->id,
                        'batch_number' => $ab->batch->batch_number ?? null,
                        'name'         => $ab->batch->name,
                        'country'      => $ab->batch->country,
                        'status'       => $ab->batch->status,
                        'is_active'    => (bool) $ab->batch->is_active,   // ← ADD THIS
                    ] : null,
                    'processed_by'     => $ab->relationLoaded('processedBy') && $ab->processedBy ? [
                        'id'        => $ab->processedBy->id,
                        'full_name' => $ab->processedBy->full_name ?? $ab->processedBy->name,
                    ] : null,
                ])
            ),

            // ─── Legacy batches pivot (backward compat) ──
            'batches'            => $this->whenLoaded('batches', fn () =>
                $this->batches->map(fn ($batch) => [
                    'id'            => $batch->id,
                    'batch_code'    => $batch->batch_code ?? null,
                    'name'          => $batch->name,
                    'company_name'  => $batch->company?->name,
                    'pivot'         => [
                        'status'         => $batch->pivot->status,
                        'assigned_at'    => $batch->pivot->assigned_at,
                        'interview_date' => $batch->pivot->interview_date,
                        'deployed_at'    => $batch->pivot->deployed_at,
                    ],
                ])
            ),

            // ─── Timestamps ──────────────────────────────
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}