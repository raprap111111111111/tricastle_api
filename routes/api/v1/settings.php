<?php

// routes/api/v1/settings.php

use App\Http\Controllers\v1\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('settings')->name('settings.')->group(function () {

        Route::get('/',              [SettingController::class, 'index'])->name('index');
        Route::post('/',             [SettingController::class, 'store'])->name('store');
        Route::get('/{setting}',     [SettingController::class, 'show'])->name('show');
        Route::put('/{setting}',     [SettingController::class, 'update'])->name('update');
        Route::patch('/{setting}',   [SettingController::class, 'update'])->name('patch');
        Route::delete('/{setting}',  [SettingController::class, 'destroy'])->name('destroy');
    });
});