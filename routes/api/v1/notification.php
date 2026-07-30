<?php
// routes/api/v1/notification.php

use App\Http\Controllers\v1\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->controller(NotificationController::class)->group(function () {

    // ✅ Static routes FIRST
    Route::get('/',         'index')->name('notifications.index');
    Route::patch('/read-all', 'markAllAsRead')->name('notifications.markAllAsRead');

    // ✅ Dynamic routes AFTER
    Route::get('/{notification}',         'show')->name('notifications.show');
    Route::patch('/{notification}/read',  'markAsRead')->name('notifications.markAsRead');
    Route::delete('/{notification}',      'destroy')->name('notifications.destroy');
});