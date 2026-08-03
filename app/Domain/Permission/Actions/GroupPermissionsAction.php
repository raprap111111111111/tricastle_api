<?php
// app/Domain/Permission/Actions/GroupPermissionsAction.php

namespace App\Domain\Permission\Actions;

use App\Domain\Permission\Repositories\PermissionRepository;
use Illuminate\Support\Collection;

class GroupPermissionsAction
{
    public function __construct(
        private readonly PermissionRepository $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->getGroupedByModule();
    }
}