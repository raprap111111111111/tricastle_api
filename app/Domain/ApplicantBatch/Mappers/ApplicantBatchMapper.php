<?php

// app/Domain/ApplicantBatch/Mappers/ApplicantBatchMapper.php

namespace App\Domain\ApplicantBatch\Mappers;

use App\Domain\ApplicantBatch\DTOs\CreateApplicantBatchDTO;
use App\Domain\ApplicantBatch\DTOs\RecordExamResultDTO;
use App\Domain\ApplicantBatch\DTOs\RejectApplicantBatchDTO;
use App\Domain\ApplicantBatch\DTOs\ScheduleInterviewDTO;
use App\Domain\ApplicantBatch\DTOs\UpdateApplicantBatchDTO;
use App\Domain\ApplicantBatch\DTOs\UpdateApplicantBatchStatusDTO;
use App\Http\Requests\v1\ApplicantBatch\RecordExamResultRequest;
use App\Http\Requests\v1\ApplicantBatch\RejectApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\ScheduleInterviewRequest;
use App\Http\Requests\v1\ApplicantBatch\StoreApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\UpdateApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\UpdateApplicantBatchStatusRequest;

class ApplicantBatchMapper
{
    public static function fromStoreRequest(StoreApplicantBatchRequest $request): CreateApplicantBatchDTO
    {
        return new CreateApplicantBatchDTO(
            applicantId: (int) $request->validated('applicant_id'),
            batchId:     (int) $request->validated('batch_id'),
            status:      $request->validated('status', 'applied'),
            appliedAt:   $request->validated('applied_at'),
            processedBy: $request->user()?->id,
        );
    }

    public static function fromUpdateRequest(UpdateApplicantBatchRequest $request): UpdateApplicantBatchDTO
    {
        return new UpdateApplicantBatchDTO(
            status:          $request->validated('status'),
            interviewDate:   $request->validated('interview_date'),
            medicalDate:     $request->validated('medical_date'),
            examDate:        $request->validated('exam_date'),
            acceptedAt:      $request->validated('accepted_at'),
            deployedAt:      $request->validated('deployed_at'),
            examScore:       $request->validated('exam_score') !== null
                                ? (float) $request->validated('exam_score')
                                : null,
            interviewNotes:  $request->validated('interview_notes'),
            medicalNotes:    $request->validated('medical_notes'),
            rejectionReason: $request->validated('rejection_reason'),
            processedBy:     $request->user()?->id,
        );
    }

    public static function fromUpdateStatusRequest(UpdateApplicantBatchStatusRequest $request): UpdateApplicantBatchStatusDTO
    {
        return new UpdateApplicantBatchStatusDTO(
            status:      $request->validated('status'),
            processedBy: $request->user()?->id,
        );
    }

    public static function fromScheduleInterviewRequest(ScheduleInterviewRequest $request): ScheduleInterviewDTO
    {
        return new ScheduleInterviewDTO(
            interviewDate:  $request->validated('interview_date'),
            interviewNotes: $request->validated('interview_notes'),
            processedBy:    $request->user()?->id,
        );
    }

    public static function fromRecordExamResultRequest(RecordExamResultRequest $request): RecordExamResultDTO
    {
        return new RecordExamResultDTO(
            examDate:    $request->validated('exam_date'),
            examScore:   (float) $request->validated('exam_score'),
            passed:      (bool) $request->validated('passed'),
            processedBy: $request->user()?->id,
        );
    }

    public static function fromRejectRequest(RejectApplicantBatchRequest $request): RejectApplicantBatchDTO
    {
        return new RejectApplicantBatchDTO(
            rejectionReason: $request->validated('rejection_reason'),
            processedBy:     $request->user()?->id,
        );
    }
}