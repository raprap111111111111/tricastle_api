<?php

use App\Http\Controllers\v1\BatchGroupController;
use Illuminate\Support\Facades\Route;

// 1. Custom action routes MUST come before generic parameter routes
Route::post('batches/{batch}/groups/{group}/assign', [BatchGroupController::class, 'assignApplicants'])
    ->name('batches.groups.assign');

// 2. Standard parameter routes for Groups within a Batch
Route::get('batches/{batch}/groups', [BatchGroupController::class, 'index'])
    ->name('batches.groups.index');

Route::post('batches/{batch}/groups', [BatchGroupController::class, 'store'])
    ->name('batches.groups.store');

Route::get('batches/{batch}/groups/{group}', [BatchGroupController::class, 'show'])
    ->name('batches.groups.show');

Route::put('batches/{batch}/groups/{group}', [BatchGroupController::class, 'update'])
    ->name('batches.groups.update');

Route::delete('batches/{batch}/groups/{group}', [BatchGroupController::class, 'destroy'])
    ->name('batches.groups.destroy');
