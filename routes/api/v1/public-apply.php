<?php

use App\Http\Controllers\v1\Portal\PublicApplicantRegistrationController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::post('/apply', [PublicApplicantRegistrationController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('public.applicant.apply');
});