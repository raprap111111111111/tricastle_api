<?php

use App\Http\Controllers\v1\CorrectionApprovalController;
use Illuminate\Support\Facades\Route;

Route::apiResource('correction-approvals', CorrectionApprovalController::class);

Route::prefix('correction-approvals/{correction_approval}')->group(function () {
    Route::patch('approve', [CorrectionApprovalController::class, 'approve'])
        ->name('correction-approvals.approve');

    Route::patch('reject', [CorrectionApprovalController::class, 'reject'])
        ->name('correction-approvals.reject');

    Route::patch('escalate', [CorrectionApprovalController::class, 'escalate'])
        ->name('correction-approvals.escalate');
});