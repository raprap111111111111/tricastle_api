<?php

use App\Http\Controllers\v1\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
| Prefix: /api/v1  (from routes/api.php)
| Middleware: auth:api
|--------------------------------------------------------------------------
*/

Route::prefix('dashboard')->name('dashboard.')->group(function () {

    // ─── Core ─────────────────────────────────────
    Route::get('/stats', [DashboardController::class, 'stats'])
        ->name('stats');

    Route::get('/activities', [DashboardController::class, 'activities'])
        ->name('activities');

    Route::get('/overview', [DashboardController::class, 'overview'])
        ->name('overview');

    // ─── New Analytics ────────────────────────────
    Route::get('/trends', [DashboardController::class, 'trends'])
        ->name('trends');

    Route::get('/status-breakdown', [DashboardController::class, 'statusBreakdown'])
        ->name('status-breakdown');

    Route::get('/pipeline', [DashboardController::class, 'pipeline'])
        ->name('pipeline');

    Route::get('/active-batches', [DashboardController::class, 'activeBatches'])
        ->name('active-batches');

    Route::get('/quick-stats', [DashboardController::class, 'quickStats'])
        ->name('quick-stats');

    Route::get('/attention', [DashboardController::class, 'attention'])
        ->name('attention');
});