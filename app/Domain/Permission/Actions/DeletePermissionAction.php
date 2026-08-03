<?php
// app/Domain/Permission/Actions/DeletePermissionAction.php

namespace App\Domain\Permission\Actions;

use App\Domain\Permission\Repositories\PermissionRepository;
use App\Models\Permission;
use InvalidArgumentException;

class DeletePermissionAction
{
    /**
     * Core permissions that should never be deleted
     */
    private const PROTECTED_PERMISSIONS = [
        'role.viewAny',
        'role.create',
        'role.update',
        'role.delete',
        'permission.viewAny',
        'permission.manage',
    ];

    public function __construct(
        private readonly PermissionRepository $repository,
    ) {}

    public function execute(Permission $permission): void
    {
        // 🔒 Protected permissions cannot be deleted
        if (in_array($permission->name, self::PROTECTED_PERMISSIONS, true)) {
            throw new InvalidArgumentException(
                "Cannot delete protected permission '{$permission->name}'."
            );
        }

        // 🔒 Permission still assigned to roles cannot be deleted
        $rolesCount = $permission->roles()->count();

        if ($rolesCount > 0) {
            throw new InvalidArgumentException(
                "Cannot delete permission. It's assigned to {$rolesCount} role(s)."
            );
        }

        $this->repository->delete($permission);
    }
}