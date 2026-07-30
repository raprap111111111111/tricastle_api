<?php

use App\Http\Controllers\v1\QualityScoreController;
use Illuminate\Support\Facades\Route;

Route::apiResource('quality-scores', QualityScoreController::class);

Route::post(
    'quality-scores/recalculate',
    [QualityScoreController::class, 'recalculate']
)->name('quality-scores.recalculate');