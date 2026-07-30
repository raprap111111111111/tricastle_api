<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'name'           => $this->name,
            'name_japanese'  => $this->name_japanese,

            'category_id'    => $this->category_id,
            'category'       => $this->whenLoaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),

            'address'        => $this->address,
            'city'           => $this->city,
            'prefecture'     => $this->prefecture,
            'postal_code'    => $this->postal_code,
            'country'        => $this->country,

            'contact_person' => $this->contact_person,
            'contact_email'  => $this->contact_email,
            'contact_phone'  => $this->contact_phone,

            'description'    => $this->description,
            'is_active'      => (bool) $this->is_active,

            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
            'deleted_at'     => $this->whenNotNull($this->deleted_at?->toIso8601String()),
        ];
    }
}