<?php
// routes/api/v1/login-history.php

use App\Http\Controllers\v1\LoginHistoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('login-histories')->controller(LoginHistoryController::class)->group(function () {

    // ─── Static routes first ───────────────────────────────────
    Route::get('/',    'index')->name('login-histories.index');
    Route::post('/',   'store')->name('login-histories.store');

    // ─── Dynamic routes after ──────────────────────────────────
    Route::get('/{login_history}',            'show')->name('login-histories.show');
    Route::patch('/{login_history}/logout',   'recordLogout')->name('login-histories.logout');
    Route::delete('/{login_history}',         'destroy')->name('login-histories.destroy');
});