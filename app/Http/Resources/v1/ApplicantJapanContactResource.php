<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantJapanContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'affiliation_type' => $this->affiliation_type, // marucon | non_marucon
            'name'             => $this->name,
            'batch_no'         => $this->batch_no,
            'company_name'     => $this->company_name,
            'relation'         => $this->relation,
            'contact_number'   => $this->contact_number,
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}