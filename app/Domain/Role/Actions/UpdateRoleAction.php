<?php
// app/Domain/Role/Actions/UpdateRoleAction.php

namespace App\Domain\Role\Actions;

use App\Domain\Role\DTOs\UpdateRoleDTO;
use App\Domain\Role\Repositories\RoleRepository;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;

class UpdateRoleAction
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function execute(Role $role, UpdateRoleDTO $dto): Role
    {
        // 🔒 Domain rule: system roles are immutable
        if ($role->is_system ?? false) {
            throw new InvalidArgumentException(
                'System roles cannot be modified.'
            );
        }

        return $this->repository->update($role, [
            'name'        => $dto->name,
            'description' => $dto->description,
        ]);
    }
}