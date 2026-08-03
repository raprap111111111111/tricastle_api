<?php
// app/Policies/PermissionPolicy.php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('permission.viewAny');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permission.viewAny');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('permission.manage');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('permission.manage');
    }

    public function delete(User $user, Permission $permission): bool
    {
        // Extra guard: permissions in use cannot be deleted
        if ($permission->roles()->count() > 0) {
            return false;
        }

        return $user->hasPermissionTo('permission.manage');
    }
}