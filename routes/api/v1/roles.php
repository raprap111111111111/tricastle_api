<?php
// routes/api/v1/roles.php

use App\Http\Controllers\v1\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->name('roles.')->group(function () {

    Route::get('/',                       [RoleController::class, 'index'])->name('index');
    Route::post('/',                      [RoleController::class, 'store'])->name('store');
    Route::get('/{role}',                 [RoleController::class, 'show'])->name('show');
    Route::put('/{role}',                 [RoleController::class, 'update'])->name('update');
    Route::delete('/{role}',              [RoleController::class, 'destroy'])->name('destroy');

    Route::put('/{role}/permissions',     [RoleController::class, 'syncPermissions'])->name('sync-permissions');
    Route::get('/{role}/permissions',     [RoleController::class, 'getPermissions'])->name('permissions');

});