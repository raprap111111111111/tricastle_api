<?php
// app/Domain/Permission/Actions/CreatePermissionAction.php

namespace App\Domain\Permission\Actions;

use App\Domain\Permission\DTOs\CreatePermissionDTO;
use App\Domain\Permission\Repositories\PermissionRepository;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class CreatePermissionAction
{
    public function __construct(
        private readonly PermissionRepository $repository,
    ) {}

    public function execute(CreatePermissionDTO $dto): Permission
    {
        $permission = $this->repository->create($dto);

        Log::info('Permission created', [
            'name'   => $permission->name,
            'module' => $permission->module,
        ]);

        return $permission;
    }
}