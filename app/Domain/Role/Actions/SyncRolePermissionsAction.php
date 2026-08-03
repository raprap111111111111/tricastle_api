<?php
// app/Domain/Role/Actions/SyncRolePermissionsAction.php

namespace App\Domain\Role\Actions;

use App\Domain\Role\DTOs\SyncPermissionsDTO;
use App\Domain\Role\Repositories\RoleRepository;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class SyncRolePermissionsAction
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function execute(Role $role, SyncPermissionsDTO $dto): Role
    {
        // 🔒 Domain rule: system roles are immutable
        if ($role->is_system ?? false) {
            throw new InvalidArgumentException(
                'System roles cannot be modified.'
            );
        }

        $updated = $this->repository->syncPermissions($role, $dto->permissions);

        Log::info('Role permissions synced', [
            'role'        => $role->name,
            'permissions' => $dto->permissions,
        ]);

        return $updated;
    }
}