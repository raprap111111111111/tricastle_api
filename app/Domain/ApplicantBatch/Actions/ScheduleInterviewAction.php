<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\ScheduleInterviewDTO;
use App\Enums\ApplicantBatchStatus;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class ScheduleInterviewAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, ScheduleInterviewDTO $dto): ApplicantBatch
    {
        return $this->repository->update($applicantBatch->id, array_filter([
            'status'          => ApplicantBatchStatus::INTERVIEW_SCHEDULED->value,
            'interview_date'  => $dto->interviewDate,
            'interview_notes' => $dto->interviewNotes,
            'processed_by'    => $dto->processedBy,
        ], fn ($value) => $value !== null));
    }
}