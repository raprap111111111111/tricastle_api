<?php

namespace App\Http\Resources\v1;

use App\Enums\CivilStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantGuarantorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $civilStatus = $this->civil_status;
        if ($civilStatus instanceof CivilStatus) {
            $civilStatus = $civilStatus->value;
        }

        return [
            'id'                       => $this->id,
            'sequence'                 => $this->sequence,
            'full_name'                => $this->full_name,
            'date_of_birth'            => $this->date_of_birth?->format('Y-m-d'),
            'age'                      => $this->computed_age ?? $this->age,
            'civil_status'             => $civilStatus,
            'nationality'              => $this->nationality,
            'address'                  => $this->address,
            'residence_cert_no'        => $this->residence_cert_no,
            'residence_cert_issued_at' => $this->residence_cert_issued_at?->format('Y-m-d'),
            'residence_cert_place'     => $this->residence_cert_place,
            'relationship'             => $this->relationship,
        ];
    }
}