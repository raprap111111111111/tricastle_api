<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\UpdateApplicantBatchDTO;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class UpdateApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, UpdateApplicantBatchDTO $dto): ApplicantBatch
    {
        $payload = array_filter([
            'status'           => $dto->status,
            'interview_date'   => $dto->interviewDate,
            'medical_date'     => $dto->medicalDate,
            'exam_date'        => $dto->examDate,
            'accepted_at'      => $dto->acceptedAt,
            'deployed_at'      => $dto->deployedAt,
            'exam_score'       => $dto->examScore,
            'interview_notes'  => $dto->interviewNotes,
            'medical_notes'    => $dto->medicalNotes,
            'rejection_reason' => $dto->rejectionReason,
            'processed_by'     => $dto->processedBy,
        ], fn ($value) => $value !== null);

        return $this->repository->update($applicantBatch->id, $payload);
    }
}