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

            // Personal
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

            // Physical
            'height_cm'          => $this->height_cm ? (float) $this->height_cm : null,
            'weight_kg'          => $this->weight_kg ? (float) $this->weight_kg : null,
            'dominant_hand'      => $this->dominant_hand,
            'blood_type'         => $this->blood_type,

            // Address
            'current_address'    => $this->current_address,
            'permanent_address'  => $this->permanent_address,
            'city'               => $this->city,
            'province'           => $this->province,
            'postal_code'        => $this->postal_code,

            // Passport / IDs
            'passport_number'    => $this->passport_number,
            'passport_expiry'    => $this->passport_expiry?->format('Y-m-d'),
            'sss_number'         => $this->sss_number,
            'tin_number'         => $this->tin_number,
            'philhealth_number'  => $this->philhealth_number,
            'pagibig_number'     => $this->pagibig_number,

            // Status
            'status'             => $this->status,
            'quality_score'      => (float) $this->quality_score,
            'quality_grade'      => $this->quality_grade,

            // ─── Relations (auto-load via $with, expose via whenLoaded) ────
            'assigned_staff'     => $this->whenLoaded('assignedStaff', fn () => [
                'id'   => $this->assignedStaff->id,
                'name' => $this->assignedStaff->full_name ?? $this->assignedStaff->name,
            ]),

            'creator'            => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator->id,
                'name' => $this->creator->full_name ?? $this->creator->name,
            ]),

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

            'batches'            => $this->whenLoaded('batches', fn () =>
                $this->batches->map(fn ($batch) => [
                    'id'            => $batch->id,
                    'batch_code'    => $batch->batch_code,
                    'name'          => $batch->name,
                    'company_name'  => $batch->company?->name,
                    'pivot'         => [
                        'status'         => $batch->pivot->status,
                        'applied_at'     => $batch->pivot->applied_at,
                        'interview_date' => $batch->pivot->interview_date,
                        'deployed_at'    => $batch->pivot->deployed_at,
                    ],
                ])
            ),

            // Timestamps
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}