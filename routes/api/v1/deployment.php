<?php

use App\Http\Controllers\v1\DeploymentController;
use Illuminate\Support\Facades\Route;

// ─── Dashboard / Metadata ───────────────────────────────
Route::get('deployments/stats', [DeploymentController::class, 'stats'])
    ->name('deployments.stats');

Route::get('deployments/countries', [DeploymentController::class, 'countries'])
    ->name('deployments.countries');

// 🚀 NEW: Available deployment types (for dropdowns)
Route::get('deployments/types', [DeploymentController::class, 'types'])
    ->name('deployments.types');

// ─── Bulk operations ────────────────────────────────────
Route::post('deployments/bulk', [DeploymentController::class, 'bulk'])
    ->name('deployments.bulk');

// ─── Main resource ──────────────────────────────────────
Route::get('deployments', [DeploymentController::class, 'index'])
    ->name('deployments.index');

Route::get('deployments/{deployment}', [DeploymentController::class, 'show'])
    ->name('deployments.show');

Route::put('deployments/{deployment}', [DeploymentController::class, 'update'])
    ->name('deployments.update');

// ─── Status transitions ─────────────────────────────────
Route::patch('deployments/{deployment}/deploy', [DeploymentController::class, 'deploy'])
    ->name('deployments.deploy');

Route::patch('deployments/{deployment}/cancel', [DeploymentController::class, 'cancel'])
    ->name('deployments.cancel');