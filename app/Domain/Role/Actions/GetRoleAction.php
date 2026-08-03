<?php
// app/Domain/Role/Actions/GetRoleAction.php

namespace App\Domain\Role\Actions;

use App\Domain\Role\Repositories\RoleRepository;
use App\Models\Role; // ← Changed

class GetRoleAction
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function execute(int $id): Role
    {
        return $this->repository->findRoleById($id);
    }
}