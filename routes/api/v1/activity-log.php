<?php

use App\Http\Controllers\v1\ActivityLogController;
use Illuminate\Support\Facades\Route;

// ✅ Read-only — logs are never created/updated/deleted via API
Route::get('activity-logs', [ActivityLogController::class, 'index'])
    ->name('activity-logs.index');

Route::get('activity-logs/{activity_log}', [ActivityLogController::class, 'show'])
    ->name('activity-logs.show');