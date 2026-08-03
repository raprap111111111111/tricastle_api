<?php
// app/Domain/Permission/Actions/GetPermissionAction.php

namespace App\Domain\Permission\Actions;

use App\Domain\Permission\Repositories\PermissionRepository;
use App\Models\Permission;

class GetPermissionAction
{
    public function __construct(
        private readonly PermissionRepository $repository,
    ) {}

    public function execute(int $id): Permission
    {
        return $this->repository->findById($id);
    }
}