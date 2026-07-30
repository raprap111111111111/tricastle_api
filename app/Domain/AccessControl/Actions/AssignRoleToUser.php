<?php

namespace App\Domain\AccessControl\Actions;

use App\Models\User;
use App\Domain\AccessControl\Repositories\RoleRepository;

class AssignRoleToUser
{
    public function __construct(
        private readonly RoleRepository $repository
    ) {}

    public function execute(User $user, string $roleName): User
    {
        // Optional: Ensure role exists via repository before assigning
        $role = $this->repository->findRoleByName($roleName);

        // Sync entirely replaces old roles with the new one (standard for most systems)
        // Use assignRole() if users can have MULTIPLE roles
        $user->syncRoles([$role->name]);

        return $user->fresh('roles');
    }
}