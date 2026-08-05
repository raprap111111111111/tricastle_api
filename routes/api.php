<?php
// routes/api.php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ============================================
    // 🔓 PUBLIC ROUTES
    // ============================================
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    });

    // ============================================
    // 🔒 PROTECTED ROUTES
    // ============================================
    Route::middleware('auth:api')->group(function () {

        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::put('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
            Route::put('/preferences', [AuthController::class, 'updatePreferences'])->name('auth.preferences'); // ✅ FIXED
        });

        // ============================================
        // 📦 AUTO-LOAD ALL MODULE ROUTES
        // ============================================
        foreach (glob(__DIR__ . '/api/v1/*.php') as $routeFile) {
            require $routeFile;
        }

    });

});