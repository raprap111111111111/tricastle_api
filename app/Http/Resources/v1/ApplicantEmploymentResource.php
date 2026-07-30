<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantEmploymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'applicant_id'       => $this->applicant_id,

            'applicant'          => $this->whenLoaded('applicant', function () {
                return [
                    'id'   => $this->applicant->id,
                    'name' => $this->applicant->name ?? null,
                ];
            }),

            'company_name'       => $this->company_name,
            'position'           => $this->position,
            'industry'           => $this->industry,
            'job_description'    => $this->job_description,

            'date_started'       => $this->date_started?->toDateString(),
            'date_ended'         => $this->date_ended?->toDateString(),
            'is_current'         => (bool) $this->is_current,
            'duration_months'    => $this->duration_months,

            'country'            => $this->country,
            'city'               => $this->city,

            'salary'             => $this->salary !== null ? (float) $this->salary : null,
            'salary_currency'    => $this->salary_currency,

            'reason_for_leaving' => $this->reason_for_leaving,

            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}