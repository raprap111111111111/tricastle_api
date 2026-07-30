<?php

use App\Http\Controllers\v1\BatchController;
use Illuminate\Support\Facades\Route;

// 1. Static routes MUST come before parameter routes ({batch})
Route::get('batches/active', [BatchController::class, 'active'])
    ->name('batches.active');

// 2. Custom parameter routes
Route::patch('batches/{batch}/status', [BatchController::class, 'updateStatus'])
    ->name('batches.update-status');

Route::patch('batches/{batch}/activate', [BatchController::class, 'activate'])
    ->name('batches.activate');

Route::patch('batches/{batch}/deactivate', [BatchController::class, 'deactivate'])
    ->name('batches.deactivate');

// 3. API Resource MUST be last (contains the generic GET batches/{batch})
Route::apiResource('batches', BatchController::class);