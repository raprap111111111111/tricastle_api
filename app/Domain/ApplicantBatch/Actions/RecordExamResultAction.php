<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\RecordExamResultDTO;
use App\Enums\ApplicantBatchStatus;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class RecordExamResultAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, RecordExamResultDTO $dto): ApplicantBatch
    {
        return $this->repository->update($applicantBatch->id, array_filter([
            'status'       => $dto->passed
                ? ApplicantBatchStatus::EXAM_PASSED->value
                : ApplicantBatchStatus::EXAM_FAILED->value,
            'exam_date'    => $dto->examDate,
            'exam_score'   => $dto->examScore,
            'processed_by' => $dto->processedBy,
        ], fn ($value) => $value !== null));
    }
}