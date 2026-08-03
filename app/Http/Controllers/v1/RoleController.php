<?php
// app/Http/Controllers/v1/RoleController.php

namespace App\Http\Controllers\v1;

use App\Domain\Role\Actions\CreateRoleAction;
use App\Domain\Role\Actions\DeleteRoleAction;
use App\Domain\Role\Actions\GetRoleAction;
use App\Domain\Role\Actions\GetRolePermissionsAction;
use App\Domain\Role\Actions\ListRolesAction;
use App\Domain\Role\Actions\SyncRolePermissionsAction;
use App\Domain\Role\Actions\UpdateRoleAction;
use App\Domain\Role\Mappers\RoleMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Role\DeleteRoleRequest;
use App\Http\Requests\v1\Role\GetAllRolesRequest;
use App\Http\Requests\v1\Role\GetRoleRequest;
use App\Http\Requests\v1\Role\StoreRoleRequest;
use App\Http\Requests\v1\Role\SyncPermissionsRequest;
use App\Http\Requests\v1\Role\UpdateRoleRequest;
use App\Http\Resources\v1\RolePermissionResource;
use App\Http\Resources\v1\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        private readonly ListRolesAction           $listAction,
        private readonly GetRoleAction             $getAction,
        private readonly CreateRoleAction          $createAction,
        private readonly UpdateRoleAction          $updateAction,
        private readonly DeleteRoleAction          $deleteAction,
        private readonly SyncRolePermissionsAction $syncAction,
        private readonly GetRolePermissionsAction  $getPermissionsAction,
    ) {}

    /**
     * 📋 GET /roles
     */
    public function index(GetAllRolesRequest $request): JsonResponse
    {
        $roles = $this->listAction->execute();

        return $this->responseSuccess(
            RoleResource::collection($roles),
            'Roles retrieved successfully'
        );
    }

    /**
     * 👁️ GET /roles/{role}
     */
    public function show(GetRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->getAction->execute($role->id);

        return $this->responseSuccess(
            new RoleResource($role),
            'Role retrieved successfully'
        );
    }

    /**
     * ➕ POST /roles
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->createAction->execute(
            RoleMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new RoleResource($role),
            'Role created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * ✏️ PUT /roles/{role}
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $role,
            RoleMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new RoleResource($updated),
            'Role updated successfully'
        );
    }

    /**
     * 🗑️ DELETE /roles/{role}
     */
    public function destroy(DeleteRoleRequest $request, Role $role): JsonResponse
    {
        $this->deleteAction->execute($role);

        return $this->responseSuccess(null, 'Role deleted successfully');
    }

    /**
     * 🔗 PUT /roles/{role}/permissions
     */
    public function syncPermissions(SyncPermissionsRequest $request, Role $role): JsonResponse
    {
        $updated = $this->syncAction->execute(
            $role,
            RoleMapper::fromSyncRequest($request)
        );

        return $this->responseSuccess(
            new RoleResource($updated),
            'Permissions synced successfully'
        );
    }

    /**
     * 📋 GET /roles/{role}/permissions
     */
    public function getPermissions(Role $role): JsonResponse
    {
        $permissions = $this->getPermissionsAction->execute($role);

        return $this->responseSuccess(
            [
                'role'        => $role->name,
                'total'       => $permissions->count(),
                'permissions' => RolePermissionResource::collection($permissions),
            ],
            'Role permissions retrieved successfully'
        );
    }
}