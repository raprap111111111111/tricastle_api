<?php

use App\Http\Controllers\v1\CompanyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('companies', CompanyController::class);

Route::patch(
    'companies/{company}/toggle-status',
    [CompanyController::class, 'toggleStatus']
)->name('companies.toggle-status');