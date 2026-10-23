<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassportIssuingOfficeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'code'       => $this->code,
            'region'     => $this->region,
            'name'       => $this->name,
            'address'    => $this->address,
            'city'       => $this->city,
            'province'   => $this->province,
            'is_active'  => (bool) $this->is_active,
            'sort_order' => (int) ($this->sort_order ?? 0),
            'notes'      => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}