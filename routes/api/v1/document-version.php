<?php

use App\Http\Controllers\v1\DocumentVersionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('document-versions', DocumentVersionController::class)
    ->except(['update']);

Route::patch(
    'document-versions/{document_version}/set-current',
    [DocumentVersionController::class, 'setCurrent']
)->name('document-versions.set-current');