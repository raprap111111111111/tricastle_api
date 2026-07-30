<?php

// routes/api/v1/comments.php

use App\Http\Controllers\v1\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('comments')->name('comments.')->group(function () {

        Route::get('/',             [CommentController::class, 'index'])->name('index');
        Route::post('/',            [CommentController::class, 'store'])->name('store');
        Route::get('/{comment}',    [CommentController::class, 'show'])->name('show');
        Route::put('/{comment}',    [CommentController::class, 'update'])->name('update');
        Route::patch('/{comment}',  [CommentController::class, 'update'])->name('patch');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });
});