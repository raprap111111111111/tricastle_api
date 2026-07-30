<?php

use App\Http\Controllers\v1\DocumentVerificationController;
use App\Http\Controllers\v1\VerificationMismatchController;
use Illuminate\Support\Facades\Route;

// routes/v1/document-verification.php
Route::prefix('document-verifications/{document_verification}')->group(function () {
    Route::get('mismatches', [VerificationMismatchController::class, 'indexByVerification']);
    Route::get('steps',      [DocumentVerificationController::class, 'steps']);
    Route::patch('start',    [DocumentVerificationController::class, 'start'])->name('document-verifications.start');
    Route::patch('complete', [DocumentVerificationController::class, 'complete'])->name('document-verifications.complete');
    Route::patch('approve',  [DocumentVerificationController::class, 'approve'])->name('document-verifications.approve');
    Route::patch('reject',   [DocumentVerificationController::class, 'reject'])->name('document-verifications.reject');
});

Route::apiResource('document-verifications', DocumentVerificationController::class);
