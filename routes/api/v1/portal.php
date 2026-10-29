<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\Portal\PortalAuthController;
use App\Http\Controllers\v1\Portal\PortalProfileController;

Route::prefix('portal')->group(function () {

    // Public Applicant Portal Routes
    Route::post('/verify-claim', [PortalAuthController::class, 'verify'])
        ->middleware('throttle:10,1');
    Route::post('/register', [PortalAuthController::class, 'register']);
    Route::post('/login', [PortalAuthController::class, 'login']);

    // Authenticated Applicant Routes (Sanctum Token Auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [PortalProfileController::class, 'me']);
        Route::post('/profile-updates', [PortalProfileController::class, 'submitUpdate']);
        Route::post('/logout', [PortalAuthController::class, 'logout']);
    });
});