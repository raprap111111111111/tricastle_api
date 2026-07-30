<?php

use App\Http\Controllers\v1\ApplicantEmploymentController;
use Illuminate\Support\Facades\Route;

Route::apiResource('applicant-employments', ApplicantEmploymentController::class);

Route::patch(
    'applicant-employments/{applicant_employment}/mark-as-current',
    [ApplicantEmploymentController::class, 'markAsCurrent']
)->name('applicant-employments.mark-as-current');

Route::get(
    'applicant-employments/by-applicant/{applicant}',
    [ApplicantEmploymentController::class, 'listByApplicant']
)->name('applicant-employments.by-applicant');