<?php

use App\Http\Controllers\v1\VerificationMismatchController;
use Illuminate\Support\Facades\Route;

Route::apiResource('verification-mismatches', VerificationMismatchController::class);

Route::prefix('verification-mismatches/{verification_mismatch}')->group(function () {
    Route::patch('resolve', [VerificationMismatchController::class, 'resolve'])
        ->name('verification-mismatches.resolve');

    Route::patch('waive', [VerificationMismatchController::class, 'waive'])
        ->name('verification-mismatches.waive');

    Route::patch('escalate', [VerificationMismatchController::class, 'escalate'])
        ->name('verification-mismatches.escalate');
});