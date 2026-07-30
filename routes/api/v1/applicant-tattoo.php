<?php

use App\Http\Controllers\v1\ApplicantTattooController;
use Illuminate\Support\Facades\Route;

Route::apiResource('applicant-tattoos', ApplicantTattooController::class);

Route::patch(
    'applicant-tattoos/{applicant_tattoo}/toggle-visibility',
    [ApplicantTattooController::class, 'toggleVisibility']
)->name('applicant-tattoos.toggle-visibility');

Route::get(
    'applicant-tattoos/by-applicant/{applicant}',
    [ApplicantTattooController::class, 'listByApplicant']
)->name('applicant-tattoos.by-applicant');