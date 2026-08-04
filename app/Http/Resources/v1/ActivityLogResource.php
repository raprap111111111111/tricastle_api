<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $properties = is_array($this->properties)
            ? $this->properties
            : ($this->properties?->toArray() ?? []);

        return [
            'id'           => $this->id,
            'user_id'      => $this->causer_id,
            'user'         => $this->whenLoaded('causer', fn () => $this->causer ? [
                'id'        => $this->causer->id,
                'full_name' => $this->causer->full_name ?? $this->causer->name,
                'name'      => $this->causer->full_name ?? $this->causer->name,
                'email'     => $this->causer->email,
            ] : null),

            'action'       => $this->event ?? 'action',
            'module'       => $this->log_name ?? 'general',
            'subject_type' => $this->subject_type,
            'subject_id'   => $this->subject_id,
            'description'  => $this->description,

            'old_values'   => $properties['old']        ?? null,
            'new_values'   => $properties['attributes'] ?? null,
            'metadata'     => $properties['metadata']   ?? null,

            'ip_address'   => $properties['ip']         ?? null,
            'user_agent'   => $properties['user_agent'] ?? null,
            'url'          => $properties['url']        ?? null,
            'method'       => $properties['method']     ?? null,

            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}