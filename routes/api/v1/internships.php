<?php

use App\Http\Controllers\MOAController;
use App\Http\Controllers\v1\ApplicantGuarantorController;
use App\Http\Controllers\v1\InternshipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Internship & MOA Routes (Auto-loaded under /api/v1 with Passport auth:api)
|--------------------------------------------------------------------------
*/

// ─── 1. Bulk MOA Generation (MUST be before {internship} parameter) ───────
Route::post('internships/moa/bulk-generate', [InternshipController::class, 'bulkGenerateMoa'])
    ->name('internships.moa.bulk-generate');

// ─── 2. MOA Actions on Single Internship ─────────────────────────────────
Route::get('internships/{internship}/download-moa', [MOAController::class, 'generateMOA'])
    ->name('internships.download-moa');

Route::post('internships/{internship}/generate-moa', [InternshipController::class, 'generateMoa'])
    ->name('internships.generate-moa');

Route::post('internships/{internship}/change-company', [InternshipController::class, 'changeCompany'])
    ->name('internships.change-company');

// ─── 3. Core CRUD ────────────────────────────────────────────────────────
Route::apiResource('internships', InternshipController::class);

// ─── 4. Guarantor Routes on Applicant ────────────────────────────────────
Route::get('applicants/{applicant}/guarantors', [ApplicantGuarantorController::class, 'index'])
    ->name('applicants.guarantors.index');

Route::post('applicants/{applicant}/guarantors/sync', [ApplicantGuarantorController::class, 'sync'])
    ->name('applicants.guarantors.sync');