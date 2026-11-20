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

    // Optional: public avatar stream
    Route::get('/users/{user}/avatar', [UserController::class, 'avatar'])
        ->whereNumber('user')
        ->name('users.avatar.public');

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    });

    // ── 🟢 Public Self-Registration (QR / Walk-In) ────────────────
    // Loaded OUTSIDE auth:api so anyone can register without logging in
    require __DIR__ . '/api/v1/public-apply.php';

    // ── 🔵 Applicant Portal ──────────────────────────────────────
    // Loaded OUTSIDE auth:api because it has its own auth:applicant guard
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
        require __DIR__ . '/api/v1/profile_update_requests.php';

        // Auto-load remaining staff-only route modules
        foreach (glob(__DIR__ . '/api/v1/*.php') as $routeFile) {
            $basename = basename($routeFile);

            // 👈 CRITICAL: Skip public-apply.php, portal.php, and profile_update_requests.php
            if (in_array($basename, ['portal.php', 'profile_update_requests.php', 'public-apply.php'])) {
                continue;
            }

            require $routeFile;
        }
    });
});
