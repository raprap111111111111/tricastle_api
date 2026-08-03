<?php
// app/Http/Controllers/v1/PermissionController.php

namespace App\Http\Controllers\v1;

use App\Domain\Permission\Actions\CreatePermissionAction;
use App\Domain\Permission\Actions\DeletePermissionAction;
use App\Domain\Permission\Actions\GetPermissionAction;
use App\Domain\Permission\Actions\GroupPermissionsAction;
use App\Domain\Permission\Actions\ListPermissionsAction;
use App\Domain\Permission\Actions\UpdatePermissionAction;
use App\Domain\Permission\Mappers\PermissionMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Permission\DeletePermissionRequest;
use App\Http\Requests\v1\Permission\GetAllPermissionsRequest;
use App\Http\Requests\v1\Permission\GetPermissionRequest;
use App\Http\Requests\v1\Permission\StorePermissionRequest;
use App\Http\Requests\v1\Permission\UpdatePermissionRequest;
use App\Http\Resources\v1\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function __construct(
        private readonly ListPermissionsAction  $listAction,
        private readonly GetPermissionAction    $getAction,
        private readonly CreatePermissionAction $createAction,
        private readonly UpdatePermissionAction $updateAction,
        private readonly DeletePermissionAction $deleteAction,
        private readonly GroupPermissionsAction $groupAction,
    ) {}

    /**
     * 📋 GET /permissions
     */
    public function index(GetAllPermissionsRequest $request): JsonResponse
    {
        $permissions = $this->listAction->execute($request->validated());

        return $this->responseSuccess(
            PermissionResource::collection($permissions),
            'Permissions retrieved successfully'
        );
    }

    /**
     * 📊 GET /permissions/grouped
     */
    public function grouped(GetAllPermissionsRequest $request): JsonResponse
    {
        $grouped = $this->groupAction->execute();

        return $this->responseSuccess(
            $grouped,
            'Permissions grouped by module retrieved successfully'
        );
    }

    /**
     * 👁️ GET /permissions/{permission}
     */
    public function show(GetPermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission = $this->getAction->execute($permission->id);

        return $this->responseSuccess(
            new PermissionResource($permission),
            'Permission retrieved successfully'
        );
    }

    /**
     * ➕ POST /permissions
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->createAction->execute(
            PermissionMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new PermissionResource($permission),
            'Permission created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * ✏️ PUT /permissions/{permission}
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $permission,
            PermissionMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new PermissionResource($updated),
            'Permission updated successfully'
        );
    }

    /**
     * 🗑️ DELETE /permissions/{permission}
     */
    public function destroy(DeletePermissionRequest $request, Permission $permission): JsonResponse
    {
        $this->deleteAction->execute($permission);

        return $this->responseSuccess(null, 'Permission deleted successfully');
    }
}