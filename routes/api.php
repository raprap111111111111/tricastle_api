<?php

use App\Http\Controllers\v1\ApplicantDocumentController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\HealthController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ============================================
    // 🔓 PUBLIC ROUTES (No Auth Required)
    // ============================================

    Route::get('/health', HealthController::class)->name('health');

    // Document streaming (blob preview/download)
    Route::prefix('applicant-documents')->group(function () {
        Route::get('/{applicantDocument}/preview', [ApplicantDocumentController::class, 'preview'])
            ->whereNumber('applicantDocument');

        Route::get('/{applicantDocument}/file', [ApplicantDocumentController::class, 'file'])
            ->whereNumber('applicantDocument');

        Route::get('/{applicantDocument}/download', [ApplicantDocumentController::class, 'download'])
            ->whereNumber('applicantDocument');
    });

    // Optional: public avatar stream (so <img src="..."> works without Authorization header)
    // If you prefer avatars auth-only, remove this block and keep only the route in user.php
    Route::get('/users/{user}/avatar', [UserController::class, 'avatar'])
        ->whereNumber('user')
        ->name('users.avatar.public');

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    });

    // ── 🔵 Applicant Portal ──────────────────────
    // Loaded OUTSIDE auth:api because it has its own
    // auth:applicant (Sanctum) guard inside the file.
    require __DIR__ . '/api/v1/portal.php';

    // ============================================
    // 🔒 PROTECTED ROUTES (Staff — Passport auth:api)
    // ============================================
    Route::middleware('auth:api')->group(function () {

        // Staff auth
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
            Route::match(['post', 'put'], '/profile', [AuthController::class, 'updateProfile'])->name('auth.update-profile');
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::put('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
            Route::put('/preferences', [AuthController::class, 'updatePreferences'])->name('auth.preferences');
        });

        // ── 🟠 Staff: Profile Update Requests ────
        // Loaded INSIDE auth:api — inherits Passport guard.
        require __DIR__ . '/api/v1/profile_update_requests.php';

        // Auto-load any future staff-only route modules
        foreach (glob(__DIR__ . '/api/v1/*.php') as $routeFile) {
            $basename = basename($routeFile);
            // Skip files already loaded explicitly above
            if (in_array($basename, ['portal.php', 'profile_update_requests.php'])) {
                continue;
            }
            require $routeFile;
        }
    });
});