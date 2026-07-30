<?php

use App\Http\Controllers\v1\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Role Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/roles
| Middleware: auth:api (inherited from parent)
*/

Route::prefix('roles')->name('roles.')->group(function () {
    
    // 📋 List all roles
    Route::get('/', [RoleController::class, 'index'])
        ->middleware('permission:view_roles')
        ->name('index');
    
    // ➕ Create new role
    Route::post('/', [RoleController::class, 'store'])
        ->middleware('permission:create_role')
        ->name('store');
    
    // 👁️ Get single role with permissions
    Route::get('/{role}', [RoleController::class, 'show'])
        ->middleware('permission:view_roles')
        ->name('show');
    
    // ✏️ Update role
    Route::put('/{role}', [RoleController::class, 'update'])
        ->middleware('permission:edit_role')
        ->name('update');
    
    // 🗑️ Delete role
    Route::delete('/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:delete_role')
        ->name('destroy');
    
    // 🔗 Sync permissions to role
    Route::put('/{role}/permissions', [RoleController::class, 'syncPermissions'])
        ->middleware('permission:manage_permissions')
        ->name('sync-permissions');
    
    // 📋 Get all permissions of a role
    Route::get('/{role}/permissions', [RoleController::class, 'getPermissions'])
        ->middleware('permission:view_roles')
        ->name('permissions');
});