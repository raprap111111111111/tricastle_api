<?php

use App\Http\Controllers\v1\ApplicantEducationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('applicant-educations', ApplicantEducationController::class);

Route::get(
    'applicant-educations/by-applicant/{applicant}',
    [ApplicantEducationController::class, 'listByApplicant']
)->name('applicant-educations.by-applicant');