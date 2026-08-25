<?php
// routes/api.php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// ============================================
// 🚀 TEMPORARY ONE-TIME SEEDER ROUTE
// ============================================
$seederCallback = function () {
    set_time_limit(600); // Allow up to 10 minutes for 5,500+ records
    ini_set('memory_limit', '512M');

    try {
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\LegacyApplicantsSeeder',
            '--force' => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'LegacyApplicantsSeeder finished successfully!',
            'output'  => Artisan::output(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ], 500);
    }
};

// Accessible at: /api/run-legacy-seeder-once
Route::get('/run-legacy-seeder-once', $seederCallback);

Route::prefix('v1')->group(function () use ($seederCallback) {

    // Also accessible at: /api/v1/run-legacy-seeder-once
    Route::get('/run-legacy-seeder-once', $seederCallback);

    // ============================================
    // 🔓 PUBLIC ROUTES
    // ============================================

    // 🟢 KEEP-ALIVE HEALTH ENDPOINT (For Render.com / UptimeRobot)
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('health');

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