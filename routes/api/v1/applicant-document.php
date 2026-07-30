<?php
// routes/api/v1/applicantDocument.php

use App\Http\Controllers\v1\ApplicantDocumentController;
use Illuminate\Support\Facades\Route;

// routes/api/v1/applicantDocument.php

Route::prefix('applicant-documents')->group(function () {

    Route::get('/', [ApplicantDocumentController::class, 'index']);
    Route::post('/', [ApplicantDocumentController::class, 'store']);

    Route::get('/folders', [ApplicantDocumentController::class, 'folders']);

    Route::get('/{applicantId}/folder', [ApplicantDocumentController::class, 'folder'])
        ->whereNumber('applicantId');

    Route::post('/{applicantDocument}/verify', [ApplicantDocumentController::class, 'verify'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/reject', [ApplicantDocumentController::class, 'reject'])
        ->whereNumber('applicantDocument');

    Route::post('/{applicantDocument}/versions', [ApplicantDocumentController::class, 'uploadVersion'])
        ->whereNumber('applicantDocument');

    Route::get('/{applicantDocument}', [ApplicantDocumentController::class, 'show'])
        ->whereNumber('applicantDocument');

    Route::put('/{applicantDocument}', [ApplicantDocumentController::class, 'update'])
        ->whereNumber('applicantDocument');

    Route::delete('/{applicantDocument}', [ApplicantDocumentController::class, 'destroy'])
        ->whereNumber('applicantDocument');
});