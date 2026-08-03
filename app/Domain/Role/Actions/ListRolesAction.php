<?php
// app/Domain/Role/Actions/ListRolesAction.php

namespace App\Domain\Role\Actions;

use App\Domain\Role\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;

class ListRolesAction
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->getAllRolesWithCounts();
    }
}