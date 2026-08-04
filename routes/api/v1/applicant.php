<?php

use App\Http\Controllers\v1\ApplicantController;
use Illuminate\Support\Facades\Route;

Route::apiResource('applicants', ApplicantController::class);

// ─── Duplicates ─────────────────────────────────────────
Route::post('applicants/check-duplicates', [ApplicantController::class, 'checkDuplicates'])
    ->name('applicants.check-duplicates');

// ─── Status Transitions ─────────────────────────────────
Route::patch('applicants/{applicant}/status', [ApplicantController::class, 'updateStatus'])
    ->name('applicants.update-status');

Route::patch('applicants/{applicant}/move-to-final-list', [ApplicantController::class, 'moveToFinalList'])
    ->name('applicants.move-to-final-list');

Route::patch('applicants/{applicant}/reject', [ApplicantController::class, 'reject'])
    ->name('applicants.reject');

// ─── Staff Assignment ───────────────────────────────────
Route::patch('applicants/{applicant}/assign', [ApplicantController::class, 'assign'])
    ->name('applicants.assign');

Route::patch('applicants/{applicant}/transfer', [ApplicantController::class, 'transfer'])
    ->name('applicants.transfer');