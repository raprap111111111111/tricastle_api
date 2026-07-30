<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantEducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'applicant_id'           => $this->applicant_id,

            'applicant'              => $this->whenLoaded('applicant', function () {
                return [
                    'id'   => $this->applicant->id,
                    'name' => $this->applicant->name ?? null,
                ];
            }),

            'education_level'        => $this->education_level?->value,
            'education_level_label'  => $this->education_level?->label(),

            'education_status'       => $this->education_status?->value,
            'education_status_label' => $this->education_status?->label(),

            'school_name'            => $this->school_name,
            'course'                 => $this->course,

            'year_started'           => $this->year_started,
            'year_ended'             => $this->year_ended,

            'honors'                 => $this->honors,

            'created_at'             => $this->created_at?->toIso8601String(),
            'updated_at'             => $this->updated_at?->toIso8601String(),
        ];
    }
}