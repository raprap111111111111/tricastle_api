<?php

use App\Http\Controllers\v1\ApplicantBatchController;
use Illuminate\Support\Facades\Route;

Route::apiResource('applicant-batches', ApplicantBatchController::class);

// Status workflow endpoints
Route::patch('applicant-batches/{applicant_batch}/status',             [ApplicantBatchController::class, 'updateStatus'])->name('applicant-batches.update-status');
Route::patch('applicant-batches/{applicant_batch}/schedule-interview', [ApplicantBatchController::class, 'scheduleInterview'])->name('applicant-batches.schedule-interview');
Route::patch('applicant-batches/{applicant_batch}/record-exam',        [ApplicantBatchController::class, 'recordExamResult'])->name('applicant-batches.record-exam');
Route::patch('applicant-batches/{applicant_batch}/accept',             [ApplicantBatchController::class, 'accept'])->name('applicant-batches.accept');
Route::patch('applicant-batches/{applicant_batch}/reject',             [ApplicantBatchController::class, 'reject'])->name('applicant-batches.reject');
Route::patch('applicant-batches/{applicant_batch}/withdraw',           [ApplicantBatchController::class, 'withdraw'])->name('applicant-batches.withdraw');
Route::patch('applicant-batches/{applicant_batch}/deploy',             [ApplicantBatchController::class, 'deploy'])->name('applicant-batches.deploy');

// Convenience listings
Route::get('applicant-batches/by-applicant/{applicant}', [ApplicantBatchController::class, 'listByApplicant'])->name('applicant-batches.by-applicant');
Route::get('applicant-batches/by-batch/{batch}',         [ApplicantBatchController::class, 'listByBatch'])->name('applicant-batches.by-batch');