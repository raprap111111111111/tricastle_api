<?php

use App\Http\Controllers\v1\DocumentTypeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('document-types', DocumentTypeController::class);