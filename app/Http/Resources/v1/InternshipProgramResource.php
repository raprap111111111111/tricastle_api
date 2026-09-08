<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternshipProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'code'                    => $this->code,
            'name'                    => $this->name,
            'program_type'            => $this->program_type?->value ?? $this->program_type,
            'document_template'       => $this->document_template,
            'allows_company_transfer' => $this->allows_company_transfer,
            'contract_years'          => $this->contract_years,
            'default_job_description' => $this->default_job_description,
            'stipend_amount'          => $this->stipend_amount,
            'meal_allowance_amount'   => $this->meal_allowance_amount,
            'compensation_currency'   => $this->compensation_currency,
            'is_active'               => $this->is_active,
        ];
    }
}