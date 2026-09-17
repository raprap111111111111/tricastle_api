<?php

use App\Http\Controllers\v1\PassportIssuingOfficeController;
use Illuminate\Support\Facades\Route;

// Expose lookup tables inside authenticated API stack for form generation
Route::get('/passport-offices', [PassportIssuingOfficeController::class, 'index'])
    ->name('passport-offices.index');