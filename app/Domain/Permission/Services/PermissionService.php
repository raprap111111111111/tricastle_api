<?php
// app/Domain/Permission/Services/PermissionService.php

namespace App\Domain\Permission\Services;

use App\Domain\Permission\Repositories\PermissionRepository;

class PermissionService
{
    public function __construct(
        private readonly PermissionRepository $repository,
    ) {}

    public function getModules(): array
    {
        return $this->repository->getAllModules()->toArray();
    }
}