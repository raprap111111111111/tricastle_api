<?php

use App\Http\Controllers\v1\ApplicantController;
use App\Http\Controllers\v1\PdosCertificateController;
use Illuminate\Support\Facades\Route;

// ─── Bulk Actions & Special Endpoints ───────────────────────────────────────
Route::post('applicants/check-duplicates', [ApplicantController::class, 'checkDuplicates'])
    ->name('applicants.check-duplicates');

Route::post('applicants/export-insurance', [ApplicantController::class, 'exportInsurance'])
    ->name('applicants.export-insurance');

Route::post('applicants/bulk-final-list', [ApplicantController::class, 'bulkFinalList'])
    ->name('applicants.bulk-final-list');

Route::post('applicants/generate-pdos', [PdosCertificateController::class, 'generate'])
    ->name('applicants.generate-pdos');

// ─── Core CRUD ──────────────────────────────────────────────────────────────
Route::apiResource('applicants', ApplicantController::class)
    ->whereNumber('applicant');

// ─── Status Transitions & Actions ──────────────────────────────────────────
Route::prefix('applicants/{applicant}')
    ->whereNumber('applicant')
    ->group(function () {

        // 🎯 AIS Document Exports (Word & Excel)
        Route::get('ais-word', [ApplicantController::class, 'exportAisWord'])
            ->name('applicants.ais-word');

        Route::get('ais', [ApplicantController::class, 'exportAis'])
            ->name('applicants.ais');

        Route::patch('status', [ApplicantController::class, 'updateStatus'])
            ->name('applicants.update-status');

        Route::patch('accept-self-registered', [ApplicantController::class, 'acceptSelfRegistered'])
            ->name('applicants.accept-self-registered');

        Route::patch('move-to-final-list', [ApplicantController::class, 'moveToFinalList'])
            ->name('applicants.move-to-final-list');

        Route::patch('reject', [ApplicantController::class, 'reject'])
            ->name('applicants.reject');

        Route::patch('remarks', [ApplicantController::class, 'updateRemarks'])
            ->name('applicants.remarks');

        // ─── Staff Assignment ────────────────────────────────────────────────
        Route::patch('assign', [ApplicantController::class, 'assign'])
            ->name('applicants.assign');

        Route::patch('transfer', [ApplicantController::class, 'transfer'])
            ->name('applicants.transfer');
    });