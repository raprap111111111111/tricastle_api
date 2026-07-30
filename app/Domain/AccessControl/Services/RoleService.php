<?php

namespace App\Domain\AccessControl\Services;

use App\Domain\AccessControl\Repositories\RoleRepository;

class RoleService
{
    public function __construct(
        private readonly RoleRepository $repository
    ) {}

    public function getRolesList(): array
    {
        return $this->repository->getAllRolesWithCounts()->toArray();
    }

    public function getPermissionsList(): array
    {
        return $this->repository->getAllPermissionsGroupedByModule()->toArray();
    }
}