<?php
// routes/api/v1/document-expiry-alert.php

use App\Http\Controllers\v1\DocumentExpiryAlertController;
use Illuminate\Support\Facades\Route;

Route::prefix('document-expiry-alerts')->controller(DocumentExpiryAlertController::class)->group(function () {

    // Static routes first
    Route::get('/',                              'index')->name('document-expiry-alerts.index');
    Route::post('/',                             'store')->name('document-expiry-alerts.store');
    Route::post('/check',                        'check')->name('document-expiry-alerts.check');

    // Dynamic routes after
    Route::get('/{document_expiry_alert}',       'show')->name('document-expiry-alerts.show');
    Route::patch('/{document_expiry_alert}/dismiss', 'dismiss')->name('document-expiry-alerts.dismiss');
    Route::delete('/{document_expiry_alert}',    'destroy')->name('document-expiry-alerts.destroy');
});