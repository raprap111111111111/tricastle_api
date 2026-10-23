<?php

use App\Http\Controllers\v1\PassportIssuingOfficeController;
use Illuminate\Support\Facades\Route;

// 1. Existing GET route for dropdown lookup
Route::get('/passport-offices', [PassportIssuingOfficeController::class, 'index'])
    ->name('passport-offices.index');

// 2. Alias POST route so http.post('/passport-offices') works directly
Route::post('/passport-offices', [PassportIssuingOfficeController::class, 'store'])
    ->name('passport-offices.store');

// 3. Full API Resource CRUD routes for /passport-issuing-offices
Route::apiResource('passport-issuing-offices', PassportIssuingOfficeController::class);