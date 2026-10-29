<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\Staff\ProfileUpdateRequestController;

// 🎯 MUST be auth:api (Passport) for Staff API requests
Route::prefix('profile-update-requests')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProfileUpdateRequestController::class, 'index']);
    Route::post('/{profileUpdate}/approve', [ProfileUpdateRequestController::class, 'approve']);
    Route::post('/{profileUpdate}/decline', [ProfileUpdateRequestController::class, 'decline']);
});