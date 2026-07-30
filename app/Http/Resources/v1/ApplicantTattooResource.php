<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantTattooResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'applicant_id' => $this->applicant_id,

            'applicant'    => $this->whenLoaded('applicant', function () {
                return [
                    'id'   => $this->applicant->id,
                    'name' => $this->applicant->name ?? null,
                ];
            }),

            'location'     => $this->location,
            'size'         => $this->size?->value,
            'size_label'   => $this->size?->label(),
            'description'  => $this->description,

            'photo_path'   => $this->photo_path,
            'photo_url'    => $this->photo_url,

            'is_visible'   => (bool) $this->is_visible,

            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}