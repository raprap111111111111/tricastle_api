<?php
// routes/api.php

use App\Http\Controllers\v1\ApplicantDocumentController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ============================================
    // 🔓 PUBLIC ROUTES (No Auth Required)
    // ============================================

    // 🟢 KEEP-ALIVE HEALTH ENDPOINT
    Route::get('/health', HealthController::class)->name('health');

    // 🟢 PUBLIC DOCUMENT STREAMING
    Route::prefix('applicant-documents')->group(function () {
        Route::get('/{applicantDocument}/preview', [ApplicantDocumentController::class, 'preview'])
            ->whereNumber('applicantDocument');

        Route::get('/{applicantDocument}/file', [ApplicantDocumentController::class, 'file'])
            ->whereNumber('applicantDocument');

        Route::get('/{applicantDocument}/download', [ApplicantDocumentController::class, 'download'])
            ->whereNumber('applicantDocument');
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    });

    // ============================================
    // 🔒 PROTECTED ROUTES (Auth Required)
    // ============================================
    Route::middleware('auth:api')->group(function () {

        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
            Route::match(['post', 'put'], '/profile', [AuthController::class, 'updateProfile'])->name('auth.update-profile');
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::put('/change-password', [AuthController::class, 'changePassword'])->name('auth.change-password');
            Route::put('/preferences', [AuthController::class, 'updatePreferences'])->name('auth.preferences');
        });

        // ============================================
        // 📦 AUTO-LOAD ALL MODULE ROUTES
        // ============================================
        foreach (glob(__DIR__ . '/api/v1/*.php') as $routeFile) {
            require $routeFile;
        }

    });

});