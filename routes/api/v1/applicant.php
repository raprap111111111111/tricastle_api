<?php

use App\Http\Controllers\v1\ApplicantController;
use Illuminate\Support\Facades\Route;

// ─── Duplicate Check ────────────────────────────────────────────────────────
// Declared before apiResource to avoid route parameter collision
Route::post('applicants/check-duplicates', [ApplicantController::class, 'checkDuplicates'])
    ->name('applicants.check-duplicates');

// ─── Core CRUD ──────────────────────────────────────────────────────────────
// Registers: index, store, show, update, destroy
Route::apiResource('applicants', ApplicantController::class)
    ->whereNumber('applicant');

// ─── Status Transitions ─────────────────────────────────────────────────────
Route::prefix('applicants/{applicant}')
    ->whereNumber('applicant')
    ->group(function () {
        Route::patch('status', [ApplicantController::class, 'updateStatus'])
            ->name('applicants.update-status');

        Route::patch('move-to-final-list', [ApplicantController::class, 'moveToFinalList'])
            ->name('applicants.move-to-final-list');

        Route::patch('reject', [ApplicantController::class, 'reject'])
            ->name('applicants.reject');

        // ─── Staff Assignment ────────────────────────────────────────────────
        Route::patch('assign', [ApplicantController::class, 'assign'])
            ->name('applicants.assign');

        Route::patch('transfer', [ApplicantController::class, 'transfer'])
            ->name('applicants.transfer');
    });