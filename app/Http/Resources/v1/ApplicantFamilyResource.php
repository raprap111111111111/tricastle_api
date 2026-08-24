<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantFamilyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'spouse' => [
                'name'        => $this->spouse_name,
                'occupation'  => $this->spouse_occupation,
                'salary'      => $this->spouse_salary !== null ? (float) $this->spouse_salary : null,
                'salary_unit' => $this->spouse_salary_unit,
            ],

            'father' => [
                'name' => $this->father_name,
            ],

            'mother' => [
                'name' => $this->mother_name,
            ],

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}