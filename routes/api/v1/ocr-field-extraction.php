<?php

use App\Http\Controllers\v1\OcrFieldExtractionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('ocr-field-extractions', OcrFieldExtractionController::class);

Route::prefix('ocr-field-extractions/{ocr_field_extraction}')->group(function () {
    Route::post('correct', [OcrFieldExtractionController::class, 'correct']);
    Route::post('accept',  [OcrFieldExtractionController::class, 'accept']);
    Route::post('reject',  [OcrFieldExtractionController::class, 'reject']);
});