<?php
// routes/api/v1/permissions.php

use App\Http\Controllers\v1\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Permission Routes
|--------------------------------------------------------------------------
| Prefix: /api/v1/permissions
| Middleware: auth:api (from parent)
| Authorization: Handled by FormRequests + PermissionPolicy
*/

Route::prefix('permissions')->name('permissions.')->group(function () {

    Route::get('/',                   [PermissionController::class, 'index'])->name('index');
    Route::get('/grouped',            [PermissionController::class, 'grouped'])->name('grouped');
    Route::post('/',                  [PermissionController::class, 'store'])->name('store');
    Route::get('/{permission}',       [PermissionController::class, 'show'])->name('show');
    Route::put('/{permission}',       [PermissionController::class, 'update'])->name('update');
    Route::delete('/{permission}',    [PermissionController::class, 'destroy'])->name('destroy');

});