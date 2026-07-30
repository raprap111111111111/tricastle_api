<?php

// routes/api/v1/ocr-templates.php

use App\Http\Controllers\v1\OcrTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('ocr-templates')->name('ocr-templates.')->group(function () {

        // Standard CRUD
        Route::get('/',    [OcrTemplateController::class, 'index'])->name('index');
        Route::post('/',   [OcrTemplateController::class, 'store'])->name('store');
        Route::get('/{ocr_template}',    [OcrTemplateController::class, 'show'])->name('show');
        Route::put('/{ocr_template}',    [OcrTemplateController::class, 'update'])->name('update');
        Route::patch('/{ocr_template}',  [OcrTemplateController::class, 'update'])->name('patch');
        Route::delete('/{ocr_template}', [OcrTemplateController::class, 'destroy'])->name('destroy');

        // Custom Actions
        Route::patch('/{ocr_template}/approve',  [OcrTemplateController::class, 'approve'])->name('approve');
        Route::patch('/{ocr_template}/reject',   [OcrTemplateController::class, 'reject'])->name('reject');
        Route::patch('/{ocr_template}/complete', [OcrTemplateController::class, 'complete'])->name('complete');
        Route::patch('/{ocr_template}/cancel',   [OcrTemplateController::class, 'cancel'])->name('cancel');
    });
});