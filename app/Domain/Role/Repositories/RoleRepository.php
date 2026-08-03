<?php
// app/Domain/Role/Repositories/RoleRepository.php

namespace App\Domain\Role\Repositories;

use App\Models\Role; // ← Changed from Spatie\Permission\Models\Role

use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    // ═══════════════════════════════════════════════════════
    // Read
    // ═══════════════════════════════════════════════════════

    public function getAllRolesWithCounts(): Collection
    {
        return Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();
    }

    public function findRoleById(int $id): Role
    {
        return Role::with('permissions')
            ->withCount('users')
            ->findOrFail($id);
    }

    public function findRoleByName(string $name): Role
    {
        return Role::findByName($name, 'api');
    }

    // ═══════════════════════════════════════════════════════
    // Write
    // ═══════════════════════════════════════════════════════

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update(array_filter(
            $data,
            fn($value) => !is_null($value)
        ));

        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    public function syncPermissions(Role $role, array $permissions): Role
    {
        $role->syncPermissions($permissions);

        return $role->fresh('permissions');
    }
}