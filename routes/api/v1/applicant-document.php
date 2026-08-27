<?php

use App\Http\Controllers\v1\ApplicantDocumentController;
use Illuminate\Support\Facades\Route;

// 🟢 PUBLIC FILE STREAMING ROUTES (Allows <img src="..."> to load images without 401 errors)
Route::prefix('applicant-documents')->group(function () {
    Route::get('/{applicantDocument}/preview',  [ApplicantDocumentController::class, 'preview'])
        ->whereNumber('applicantDocument');

    Route::get('/{applicantDocument}/file',     [ApplicantDocumentController::class, 'file'])
        ->whereNumber('applicantDocument');

    Route::get('/{applicantDocument}/download', [ApplicantDocumentController::class, 'download'])
        ->whereNumber('applicantDocument');
});

// 🔒 PROTECTED DOCUMENT MANAGEMENT ROUTES
Route::prefix('applicant-documents')->middleware('auth:api')->group(function () {

    Route::get('/',  [ApplicantDocumentController::class, 'index']);
    Route::post('/', [ApplicantDocumentController::class, 'store']);

    Route::get('/expiring', [ApplicantDocumentController::class, 'expiring']);
    Route::get('/batches',  [ApplicantDocumentController::class, 'batches']);
    Route::get('/folders',  [ApplicantDocumentController::class, 'folders']);
    
    Route::get('/{applicantId}/folder', [ApplicantDocumentController::class, 'folder'])
        ->whereNumber('applicantId');

    Route::patch('/{applicantDocument}/status',  [ApplicantDocumentController::class, 'updateStatus'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/verify',   [ApplicantDocumentController::class, 'verify'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/reject',   [ApplicantDocumentController::class, 'reject'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/versions', [ApplicantDocumentController::class, 'uploadVersion'])
        ->whereNumber('applicantDocument');

    Route::get('/{applicantDocument}',    [ApplicantDocumentController::class, 'show'])
        ->whereNumber('applicantDocument');

    Route::put('/{applicantDocument}',    [ApplicantDocumentController::class, 'update'])
        ->whereNumber('applicantDocument');

    Route::delete('/{applicantDocument}', [ApplicantDocumentController::class, 'destroy'])
        ->whereNumber('applicantDocument');
});