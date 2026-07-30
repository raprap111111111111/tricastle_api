<?php

use App\Http\Controllers\v1\CompanyCategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('company-categories', CompanyCategoryController::class);

Route::patch(
    'company-categories/{company_category}/toggle-status',
    [CompanyCategoryController::class, 'toggleStatus']
)->name('company-categories.toggle-status');