<?php

use App\Http\Controllers\v1\ApplicantLifestyleController;
use Illuminate\Support\Facades\Route;

Route::prefix('applicant-lifestyles')
    ->controller(ApplicantLifestyleController::class)
    ->group(function () {
        Route::get('/',                              'index')->name('applicant-lifestyles.index');
        Route::post('/',                             'upsert')->name('applicant-lifestyles.upsert');
        Route::get('/{applicant_lifestyle}',         'show')->name('applicant-lifestyles.show');
        Route::delete('/{applicant_lifestyle}',      'destroy')->name('applicant-lifestyles.destroy');

        // Fetch by applicant_id (convenience endpoint)
        Route::get('/by-applicant/{applicant}',      'showByApplicant')->name('applicant-lifestyles.by-applicant');
    });