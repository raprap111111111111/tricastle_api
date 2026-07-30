<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * List all roles with permission counts
     */
    public function index(): JsonResponse
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Roles retrieved successfully',
            'data' => $roles,
        ]);
    }

    /**
     * Create new role
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'api',
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role->load('permissions'),
        ], 201);
    }

    /**
     * Show single role with its permissions
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        $role->loadCount('users');

        return response()->json([
            'success' => true,
            'data' => $role,
        ]);
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        // Prevent editing system roles
        if ($role->is_system ?? false) {
            return response()->json([
                'success' => false,
                'message' => 'System roles cannot be modified.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role->fresh(),
        ]);
    }

    /**
     * Delete role
     */
    public function destroy(Role $role): JsonResponse
    {
        // Prevent deleting system roles
        if ($role->is_system ?? false) {
            return response()->json([
                'success' => false,
                'message' => 'System roles cannot be deleted.',
            ], 403);
        }

        // Prevent deleting roles that have users assigned
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role. Users are still assigned to this role.',
                'meta' => [
                    'users_count' => $role->users()->count(),
                ],
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Sync permissions for a role (replaces all existing permissions)
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($validated['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'Permissions synced successfully',
            'data' => $role->load('permissions'),
        ]);
    }

    /**
     * Get all permissions assigned to a role
     */
    public function getPermissions(Role $role): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $role->permissions,
            'meta' => [
                'role' => $role->name,
                'total' => $role->permissions->count(),
            ],
        ]);
    }
}