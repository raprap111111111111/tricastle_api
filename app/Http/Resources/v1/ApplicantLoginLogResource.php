<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantLoginLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $applicant = $this->account?->applicant;

        return [
            'id'                  => $this->id,
            'email'               => $this->email,
            'status'              => $this->status,
            'ip_address'          => $this->ip_address,
            'user_agent'          => $this->user_agent,
            'password_was_hashed' => $this->password_was_hashed,
            'attempted_at'        => $this->attempted_at?->toIso8601String(),
            'applicant_id'        => $applicant?->id,
            'applicant_code'      => $applicant?->applicant_code,
            'applicant_name'      => $applicant
                ? trim(($applicant->first_name ?? '') . ' ' . ($applicant->last_name ?? ''))
                : ($this->account?->email ?? null),
        ];
    }
}