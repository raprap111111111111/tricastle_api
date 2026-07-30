<?php

use App\Http\Controllers\v1\FileRepositoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('file-repository', FileRepositoryController::class)
    ->except(['update']);

Route::delete(
    'file-repository/{file_repository}/purge',
    [FileRepositoryController::class, 'purge']
)->name('file-repository.purge');