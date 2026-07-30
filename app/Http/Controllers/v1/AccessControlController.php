<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Domain\AccessControl\Services\RoleService;
use App\Domain\AccessControl\Actions\AssignRoleToUser;
use App\Domain\AccessControl\Actions\SyncRolePermissions;
use App\Domain\AccessControl\DTOs\RoleDataDto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccessControlController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    // GET /api/v1/roles
    public function roles(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->roleService->getRolesList()
        ]);
    }

    // GET /api/v1/permissions
    public function permissions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->roleService->getPermissionsList()
        ]);
    }

    // PUT /api/v1/roles/{id}/permissions
    public function syncPermissions(Request $request, int $id, SyncRolePermissions $action): JsonResponse
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,name']
        ]);

        $dto = RoleDataDto::fromRequest($id, $request->all());
        
        try {
            $role = $action->execute($dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Permissions synced',
                'data' => $role
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }

    // POST /api/v1/users/{user}/assign-role
    public function assignUserRole(Request $request, User $user, AssignRoleToUser $action): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'exists:roles,name']
        ]);

        $updatedUser = $action->execute($user, $request->role);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned',
            'data' => $updatedUser
        ]);
    }
}