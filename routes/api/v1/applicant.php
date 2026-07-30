<?php

use App\Http\Controllers\v1\ApplicantController;
use Illuminate\Support\Facades\Route;

Route::apiResource('applicants', ApplicantController::class);
Route::post('applicants/check-duplicates', [ApplicantController::class, 'checkDuplicates'])
     ->name('applicants.check-duplicates');