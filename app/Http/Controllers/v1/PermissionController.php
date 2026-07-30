<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * List all permissions
     */
    public function index(Request $request): JsonResponse
    {
        $permissions = Permission::query()
            ->when($request->module, fn($q, $module) => $q->where('module', $module))
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Permissions retrieved successfully',
            'data' => $permissions,
            'meta' => [
                'total' => $permissions->count(),
            ],
        ]);
    }

    /**
     * Get permissions grouped by module
     */
    public function grouped(): JsonResponse
    {
        $grouped = Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                // Group by prefix before first underscore (e.g., "view_all_applicants" → "applicants")
                $parts = explode('_', $permission->name);
                return end($parts); // last word as module
            });

        return response()->json([
            'success' => true,
            'message' => 'Permissions grouped successfully',
            'data' => $grouped,
        ]);
    }

    /**
     * Get single permission
     */
    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $permission,
        ]);
    }
}