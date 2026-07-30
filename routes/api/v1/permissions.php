<?php

use App\Http\Controllers\v1\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Permission Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/permissions
| Middleware: auth:api (inherited from parent)
*/

Route::prefix('permissions')->name('permissions.')->group(function () {
    
    // 📋 List all permissions (with optional filters)
    Route::get('/', [PermissionController::class, 'index'])
        ->middleware('permission:view_permissions')
        ->name('index');
    
    // 📊 Get permissions grouped by module
    Route::get('/grouped', [PermissionController::class, 'grouped'])
        ->middleware('permission:view_permissions')
        ->name('grouped');
    
    // 👁️ Get single permission
    Route::get('/{permission}', [PermissionController::class, 'show'])
        ->middleware('permission:view_permissions')
        ->name('show');
});