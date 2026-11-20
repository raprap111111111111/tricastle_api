<?php

use App\Http\Controllers\v1\Portal\PublicApplicantRegistrationController;
use Illuminate\Support\Facades\Route;

// 🟢 PUBLIC UNPROTECTED ROUTES (No auth:sanctum)
Route::prefix('public')->group(function () {
    Route::post('/check-duplicates', [PublicApplicantRegistrationController::class, 'checkDuplicates'])
        ->middleware('throttle:30,1'); // 30 checks per min

    Route::post('/apply', [PublicApplicantRegistrationController::class, 'store'])
        ->middleware('throttle:5,1'); 
});