<?php
// app/Http/Resources/v1/LoginHistoryResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'ip_address'     => $this->ip_address,
            'user_agent'     => $this->user_agent,
            'device_type'    => $this->device_type,
            'browser'        => $this->browser,
            'platform'       => $this->platform,
            'location'       => $this->location,
            'status'         => $this->status,
            'failure_reason' => $this->failure_reason,
            'login_method'   => $this->login_method,
            'logged_in_at'   => $this->logged_in_at->toISOString(),
            'logged_out_at'  => $this->logged_out_at?->toISOString(),

            'user'           => $this->whenLoaded('user'),

            'created_at'     => $this->created_at->toISOString(),
            'updated_at'     => $this->updated_at->toISOString(),
        ];
    }
}