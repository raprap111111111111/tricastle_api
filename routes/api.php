<?php
// routes/api.php

use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ============================================
    // 🔓 PUBLIC ROUTES
    // ============================================

        // ⚠️ TEMPORARY SEEDER ROUTE (Fixed: No missing .env key errors)
    Route::get('/run-legacy-seeder', function () {
        // Simple security check: ?key=tricastle2026
        $providedKey = request()->query('key');
        
        if ($providedKey !== 'tricastle2026') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. Please provide ?key=tricastle2026 in the URL.',
            ], 403);
        }

        try {
            // Prevent execution timeout & memory limits for large CSV files
            set_time_limit(300);
            ini_set('memory_limit', '512M');

            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'LegacyApplicantsSeeder',
                '--force' => true,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'LegacyApplicantsSeeder executed successfully!',
                'output'  => \Illuminate\Support\Facades\Artisan::output(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    });

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