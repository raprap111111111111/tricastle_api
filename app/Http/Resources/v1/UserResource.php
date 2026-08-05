<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'employee_code' => $this->employee_code,
            'department' => $this->department,
            'position' => $this->position,
            'is_active' => (bool) $this->is_active,
            'last_login_at' => $this->last_login_at,
            'theme_preference'  => $this->theme_preference ?? 'default',   // ← ADD
            'effects_enabled'   => (bool) ($this->effects_enabled ?? true), // ← ADD

            'roles' => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),

            'permissions' => $this->when(
                $this->resource->relationLoaded('roles') || true,
                fn() => $this->resource->getAllPermissions()->pluck('name')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
