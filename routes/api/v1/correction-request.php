<?php

use App\Http\Controllers\v1\CorrectionRequestController;
use Illuminate\Support\Facades\Route;

Route::apiResource('correction-requests', CorrectionRequestController::class);

Route::prefix('correction-requests/{correction_request}')->group(function () {
    Route::patch('approve', [CorrectionRequestController::class, 'approve'])
        ->name('correction-requests.approve');

    Route::patch('reject', [CorrectionRequestController::class, 'reject'])
        ->name('correction-requests.reject');

    Route::patch('complete', [CorrectionRequestController::class, 'complete'])
        ->name('correction-requests.complete');

    Route::patch('cancel', [CorrectionRequestController::class, 'cancel'])
        ->name('correction-requests.cancel');
});