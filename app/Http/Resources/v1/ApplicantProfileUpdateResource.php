<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantProfileUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'applicant_id'     => $this->applicant_id,
            'status'           => $this->status,
            'proposed_payload' => $this->proposed_payload,
            'decline_reason'   => $this->decline_reason,
            'reviewed_by'      => $this->reviewed_by,
            'reviewed_at'      => $this->reviewed_at,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,

            'applicant' => $this->whenLoaded('applicant', function () {
                return [
                    'id'             => $this->applicant->id,
                    'first_name'     => $this->applicant->first_name,
                    'middle_name'    => $this->applicant->middle_name,
                    'last_name'      => $this->applicant->last_name,
                    'full_name'      => trim("{$this->applicant->first_name} {$this->applicant->last_name}"),
                    'applicant_code' => $this->applicant->applicant_code,
                    'city'           => $this->applicant->city,
                    'province'       => $this->applicant->province,
                    'mobile'         => $this->applicant->mobile,
                    'passport_number'=> $this->applicant->passport_number,
                    'status'         => $this->applicant->status ?? null,
                ];
            }),

            // Who approved / declined it
            'reviewer' => $this->whenLoaded('reviewer', function () {
                if (!$this->reviewer) {
                    return null;
                }

                $fullName = trim(
                    "{$this->reviewer->first_name} " .
                    ($this->reviewer->middle_name ? "{$this->reviewer->middle_name} " : '') .
                    "{$this->reviewer->last_name}"
                );

                return [
                    'id'          => $this->reviewer->id,
                    'first_name'  => $this->reviewer->first_name,
                    'middle_name' => $this->reviewer->middle_name,
                    'last_name'   => $this->reviewer->last_name,
                    'full_name'   => $fullName,
                    'name'        => $fullName, // frontend-friendly alias
                ];
            }),
        ];
    }
}