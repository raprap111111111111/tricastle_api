<?php
// app/Http/Resources/v1/RolePermissionResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolePermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'guard_name' => $this->guard_name,
        ];
    }
}