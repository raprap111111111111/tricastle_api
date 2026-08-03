<?php
// app/Domain/Permission/Actions/UpdatePermissionAction.php

namespace App\Domain\Permission\Actions;

use App\Domain\Permission\DTOs\UpdatePermissionDTO;
use App\Domain\Permission\Repositories\PermissionRepository;
use App\Models\Permission;
use InvalidArgumentException;

class UpdatePermissionAction
{
    /**
     * Core permissions that should never be renamed/deleted
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

    public function execute(Permission $permission, UpdatePermissionDTO $dto): Permission
    {
        // 🔒 Renaming protected permissions is blocked
        if (
            $dto->name !== null
            && $dto->name !== $permission->name
            && in_array($permission->name, self::PROTECTED_PERMISSIONS, true)
        ) {
            throw new InvalidArgumentException(
                "Cannot rename protected permission '{$permission->name}'."
            );
        }

        return $this->repository->update($permission, $dto);
    }
}