<?php

namespace App\Domain\AccessControl\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function getAllRolesWithCounts(): Collection
    {
        return Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();
    }

    public function findRoleByName(string $name): Role
    {
        return Role::findByName($name, 'api'); // Assuming Sanctum/api guard
    }

    public function findRoleById(int $id): Role
    {
        return Role::findOrFail($id);
    }

    public function getAllPermissionsGroupedByModule(): \Illuminate\Support\Collection
    {
        return Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode('_', $permission->name);
                return end($parts);
            });
    }
}