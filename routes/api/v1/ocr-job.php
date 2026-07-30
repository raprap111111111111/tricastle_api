<?php

use App\Http\Controllers\v1\OcrJobController;
use Illuminate\Support\Facades\Route;

Route::apiResource('ocr-jobs', OcrJobController::class);

Route::prefix('ocr-jobs/{ocr_job}')->group(function () {
    Route::post('queue',  [OcrJobController::class, 'queue']);
    Route::post('cancel', [OcrJobController::class, 'cancel']);
    Route::post('retry',  [OcrJobController::class, 'retry']);
    Route::post('review', [OcrJobController::class, 'review']);
});