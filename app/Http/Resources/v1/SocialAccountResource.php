<?php
// app/Http/Resources/v1/SocialAccountResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'provider'         => $this->provider,
            'provider_id'      => $this->provider_id,
            'avatar'           => $this->avatar,
            'token_expires_at' => $this->token_expires_at?->toISOString(),

            'user'             => $this->whenLoaded('user'),

            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
        ];
    }
}