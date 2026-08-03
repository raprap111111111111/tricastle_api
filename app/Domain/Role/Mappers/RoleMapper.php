<?php
// app/Domain/Role/Mappers/RoleMapper.php

namespace App\Domain\Role\Mappers;

use App\Domain\Role\DTOs\CreateRoleDTO;
use App\Domain\Role\DTOs\SyncPermissionsDTO;
use App\Domain\Role\DTOs\UpdateRoleDTO;
use App\Http\Requests\v1\Role\StoreRoleRequest;
use App\Http\Requests\v1\Role\SyncPermissionsRequest;
use App\Http\Requests\v1\Role\UpdateRoleRequest;

class RoleMapper
{
    public static function fromCreateRequest(StoreRoleRequest $request): CreateRoleDTO
    {
        return new CreateRoleDTO(
            name:        $request->validated('name'),
            description: $request->validated('description'),
            permissions: $request->validated('permissions', []),
        );
    }

    public static function fromUpdateRequest(UpdateRoleRequest $request): UpdateRoleDTO
    {
        return new UpdateRoleDTO(
            name:        $request->validated('name'),
            description: $request->validated('description'),
        );
    }

    public static function fromSyncRequest(SyncPermissionsRequest $request): SyncPermissionsDTO
    {
        return new SyncPermissionsDTO(
            permissions: $request->validated('permissions', []),
        );
    }
}