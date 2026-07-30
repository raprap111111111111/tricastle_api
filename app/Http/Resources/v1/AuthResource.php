<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->resource['access_token'] ?? null,
            'token_type' => $this->resource['token_type'] ?? 'Bearer',
            'expires_at' => $this->resource['expires_at'] ?? null,
        ];
    }
}