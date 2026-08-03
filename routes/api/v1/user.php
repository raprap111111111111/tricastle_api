<?php
// routes/api/v1/user.php

use App\Http\Controllers\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);

Route::prefix('users/{user}')->group(function () {
    Route::post('toggle-active', [UserController::class, 'toggleActive'])
         ->name('users.toggle-active');
    Route::post('assign-roles', [UserController::class, 'assignRoles'])
         ->name('users.assign-roles');
    Route::post('reset-password', [UserController::class, 'resetPassword'])
         ->name('users.reset-password');
});