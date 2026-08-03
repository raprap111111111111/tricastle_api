<?php
// app/Http/Resources/v1/PermissionResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'guard_name'  => $this->guard_name,
            'description' => $this->description,
            'module'      => $this->module,
            'roles_count' => $this->whenCounted('roles'),
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
        ];
    }
}