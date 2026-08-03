<?php
// app/Policies/RolePolicy.php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Super admin bypasses ALL checks
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * ─────────────────────────────────────────────
     * View list
     * ─────────────────────────────────────────────
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('role.viewAny');
    }

    /**
     * ─────────────────────────────────────────────
     * View single
     * ─────────────────────────────────────────────
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('role.view');
    }

    /**
     * ─────────────────────────────────────────────
     * Create
     * ─────────────────────────────────────────────
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('role.create');
    }

    /**
     * ─────────────────────────────────────────────
     * Update — extra guard: system roles are immutable
     * ─────────────────────────────────────────────
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        return $user->hasPermissionTo('role.update');
    }

    /**
     * ─────────────────────────────────────────────
     * Delete — extra guards
     * ─────────────────────────────────────────────
     */
    public function delete(User $user, Role $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        if ($role->users()->count() > 0) {
            return false;
        }

        return $user->hasPermissionTo('role.delete');
    }

    /**
     * ─────────────────────────────────────────────
     * Sync/manage permissions on a role
     * ─────────────────────────────────────────────
     */
    public function managePermissions(User $user, Role $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        return $user->hasPermissionTo('permission.manage');
    }

    /**
     * ─────────────────────────────────────────────
     * View permissions attached to a role
     * ─────────────────────────────────────────────
     */
    public function viewPermissions(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('role.viewAny');
    }
}