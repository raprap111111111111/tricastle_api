<?php
// app/Domain/Permission/Actions/ListPermissionsAction.php

namespace App\Domain\Permission\Actions;

use App\Domain\Permission\Repositories\PermissionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListPermissionsAction
{
    public function __construct(
        private readonly PermissionRepository $repository,
    ) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($filters);
    }
}