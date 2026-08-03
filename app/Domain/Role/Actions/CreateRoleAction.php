<?php
// app/Domain/Role/Actions/CreateRoleAction.php

namespace App\Domain\Role\Actions;

use App\Domain\Role\DTOs\CreateRoleDTO;
use App\Domain\Role\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    public function __construct(
        private readonly RoleRepository $repository,
    ) {}

    public function execute(CreateRoleDTO $dto): Role
    {
        return DB::transaction(function () use ($dto) {

            // ── 1. Create role ──────────────────────────────
            $role = $this->repository->create([
                'name'        => $dto->name,
                'guard_name'  => 'api',
                'description' => $dto->description,
            ]);

            // ── 2. Sync permissions if provided ────────────
            if (!empty($dto->permissions)) {
                $this->repository->syncPermissions($role, $dto->permissions);

                Log::info('Permissions assigned to new role', [
                    'role'        => $role->name,
                    'permissions' => $dto->permissions,
                ]);
            }

            return $this->repository->findRoleById($role->id);
        });
    }
}