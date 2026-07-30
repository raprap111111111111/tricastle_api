<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'code'                => $this->code,
            'description'         => $this->description,
            'required_fields'     => $this->required_fields,
            'validation_rules'    => $this->validation_rules,
            'is_required'         => $this->is_required,
            'is_active'           => $this->is_active,
            'validity_days'       => $this->validity_days,
            'expiry_warning_days' => $this->expiry_warning_days,
            'category'            => $this->category,
            'sort_order'          => $this->sort_order,
            'created_at'          => $this->created_at?->toDateTimeString(),
            'updated_at'          => $this->updated_at?->toDateTimeString(),
        ];
    }
}