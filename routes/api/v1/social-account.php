<?php
// routes/api/v1/social-account.php

use App\Http\Controllers\v1\SocialAccountController;
use Illuminate\Support\Facades\Route;

Route::prefix('social-accounts')->controller(SocialAccountController::class)->group(function () {

    // ─── Static routes first ───────────────────────────────────
    Route::get('/',   'index')->name('social-accounts.index');
    Route::post('/',  'store')->name('social-accounts.store');

    // ─── Dynamic routes after ──────────────────────────────────
    Route::get('/{social_account}',    'show')->name('social-accounts.show');
    Route::delete('/{social_account}', 'destroy')->name('social-accounts.destroy');
});