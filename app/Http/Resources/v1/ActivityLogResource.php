<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'action'       => $this->action,
            'module'       => $this->module,
            'subject_type' => $this->subject_type,
            'subject_id'   => $this->subject_id,
            'description'  => $this->description,
            'old_values'   => $this->old_values,
            'new_values'   => $this->new_values,
            'metadata'     => $this->metadata,
            'ip_address'   => $this->ip_address,
            'user_agent'   => $this->user_agent,
            'url'          => $this->url,
            'method'       => $this->method,
            'has_changes'  => $this->hasChanges(),
            'changed_fields' => $this->getChangedFields(),

            // Relations
            'user'         => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),

            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}