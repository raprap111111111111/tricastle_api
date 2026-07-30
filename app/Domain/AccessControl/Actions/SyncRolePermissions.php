<?php

namespace App\Domain\AccessControl\Actions;

use App\Domain\AccessControl\DTOs\RoleDataDto;
use App\Domain\AccessControl\Repositories\RoleRepository;
use Spatie\Permission\Models\Role;
use InvalidArgumentException;

class SyncRolePermissions
{
    public function __construct(
        private readonly RoleRepository $repository
    ) {}

    public function execute(RoleDataDto $data): Role
    {
        $role = $this->repository->findRoleById($data->roleId);

        // Domain Rule: Protect system roles from being modified
        if ($role->is_system ?? false) {
            throw new InvalidArgumentException("System roles cannot be modified.");
        }

        $role->syncPermissions($data->permissions);

        return $role->fresh('permissions');
    }
}