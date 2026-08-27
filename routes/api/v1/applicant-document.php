<?php
// routes/api/v1/applicantDocument.php

use App\Http\Controllers\v1\ApplicantDocumentController;
use Illuminate\Support\Facades\Route;

Route::prefix('applicant-documents')->group(function () {

    // ── Standard CRUD ──────────────────────────────────────────────────────
    Route::get('/',  [ApplicantDocumentController::class, 'index']);
    Route::post('/', [ApplicantDocumentController::class, 'store']);

    // ── Expiring documents ────────────────────────────────────────────────
    Route::get('/expiring', [ApplicantDocumentController::class, 'expiring']);

    // ── Level 1 — Batches ─────────────────────────────────────────────────
    Route::get('/batches', [ApplicantDocumentController::class, 'batches']);

    // ── Level 2 — Applicant folder list ───────────────────────────────────
    Route::get('/folders', [ApplicantDocumentController::class, 'folders']);

    // ── Level 3 — Single applicant folder ─────────────────────────────────
    Route::get('/{applicantId}/folder', [ApplicantDocumentController::class, 'folder'])
        ->whereNumber('applicantId');

    // ── Document actions ───────────────────────────────────────────────────
    Route::patch('/{applicantDocument}/status',   [ApplicantDocumentController::class, 'updateStatus'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/verify',    [ApplicantDocumentController::class, 'verify'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/reject',    [ApplicantDocumentController::class, 'reject'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/versions',  [ApplicantDocumentController::class, 'uploadVersion'])
        ->whereNumber('applicantDocument');

    // ── 🎯 File streaming routes (Bypasses /storage/ 404 errors!) ──────────
    Route::get('/{applicantDocument}/preview',   [ApplicantDocumentController::class, 'preview'])
        ->whereNumber('applicantDocument');

    Route::get('/{applicantDocument}/file',      [ApplicantDocumentController::class, 'file'])
        ->whereNumber('applicantDocument');

    Route::get('/{applicantDocument}/download',  [ApplicantDocumentController::class, 'download'])
        ->whereNumber('applicantDocument');

    // ── Single document CRUD (Wildcard MUST BE LAST) ──────────────────────
    Route::get('/{applicantDocument}',    [ApplicantDocumentController::class, 'show'])
        ->whereNumber('applicantDocument');

    Route::put('/{applicantDocument}',    [ApplicantDocumentController::class, 'update'])
        ->whereNumber('applicantDocument');

    Route::delete('/{applicantDocument}', [ApplicantDocumentController::class, 'destroy'])
        ->whereNumber('applicantDocument');
});