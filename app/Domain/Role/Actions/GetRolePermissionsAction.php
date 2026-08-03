<?php
// app/Domain/Role/Actions/GetRolePermissionsAction.php

namespace App\Domain\Role\Actions;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class GetRolePermissionsAction
{
    public function execute(Role $role): Collection
    {
        if (! $role->relationLoaded('permissions')) {
            $role->load('permissions');
        }

        return $role->permissions;
    }
}