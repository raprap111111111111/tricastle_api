<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantGuarantorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'sequence'                 => $this->sequence,
            'full_name'                => $this->full_name,
            'age'                      => $this->age,
            'civil_status'             => $this->civil_status,
            'nationality'              => $this->nationality,
            'address'                  => $this->address,
            'residence_cert_no'        => $this->residence_cert_no,
            'residence_cert_issued_at' => $this->residence_cert_issued_at?->toDateString(),
            'residence_cert_place'     => $this->residence_cert_place,
            'relationship'             => $this->relationship,
        ];
    }
}