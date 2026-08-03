<?php
// app/Domain/Role/Services/RoleService.php

namespace App\Domain\Role\Services;

use App\Domain\Role\Repositories\RoleRepository;

class RoleService
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function getRolesList(): array
    {
        return $this->repository->getAllRolesWithCounts()->toArray();
    }
}