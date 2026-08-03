<?php
// app/Domain/Role/Actions/DeleteRoleAction.php

namespace App\Domain\Role\Actions;

use App\Domain\Role\Repositories\RoleRepository;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function execute(Role $role): void
    {
        // 🔒 Guard: system roles are protected
        if ($role->is_system ?? false) {
            throw new InvalidArgumentException(
                'System roles cannot be deleted.'
            );
        }

        // 🔒 Guard: roles with assigned users are protected
        $usersCount = $role->users()->count();

        if ($usersCount > 0) {
            throw new InvalidArgumentException(
                "Cannot delete role. {$usersCount} user(s) are still assigned to this role."
            );
        }

        $this->repository->delete($role);
    }
}