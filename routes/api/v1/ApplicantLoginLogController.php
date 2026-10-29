<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\Staff\ApplicantLoginLogController;

// Staff (Passport) — monitoring is staff-only
Route::prefix('applicant-login-logs')->middleware('auth:api')->group(function () {
    Route::get('/', [ApplicantLoginLogController::class, 'index']);
    Route::get('/unhashed-audit', [ApplicantLoginLogController::class, 'unhashedAudit']);
});