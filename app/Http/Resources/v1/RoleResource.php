<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'guard_name'        => $this->guard_name,
            'description'       => $this->description,
            'is_system'         => (bool) $this->is_system,
            'permissions_count' => $this->whenCounted('permissions'),
            'users_count'       => $this->whenCounted('users'),
            'permissions'       => RolePermissionResource::collection(
                                       $this->whenLoaded('permissions')
                                   ),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
