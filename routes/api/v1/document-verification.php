<?php

use App\Http\Controllers\v1\DocumentVerificationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('document-verifications', DocumentVerificationController::class);

Route::prefix('document-verifications/{document_verification}')->group(function () {
    Route::patch('start', [DocumentVerificationController::class, 'start'])
        ->name('document-verifications.start');

    Route::patch('complete', [DocumentVerificationController::class, 'complete'])
        ->name('document-verifications.complete');

    Route::patch('approve', [DocumentVerificationController::class, 'approve'])
        ->name('document-verifications.approve');

    Route::patch('reject', [DocumentVerificationController::class, 'reject'])
        ->name('document-verifications.reject');
});