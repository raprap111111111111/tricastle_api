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

    // GET /api/v1/dashboard/stats
    Route::get('/stats', [DashboardController::class, 'stats'])
        ->name('stats');

    // GET /api/v1/dashboard/overview  (optional summary cards)
    Route::get('/overview', [DashboardController::class, 'overview'])
        ->name('overview');

    // GET /api/v1/dashboard/activities  (recent activity feed)
    Route::get('/activities', [DashboardController::class, 'activities'])
        ->name('activities');

    // GET /api/v1/dashboard/charts/{type?}  (optional)
    Route::get('/charts/{type?}', [DashboardController::class, 'charts'])
        ->name('charts');
});