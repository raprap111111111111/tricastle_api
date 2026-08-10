<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\ReturnApplicantBatchDTO;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ReturnApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, ReturnApplicantBatchDTO $dto): ApplicantBatch
    {
        if ($applicantBatch->status !== ApplicantBatchStatus::DEPLOYED) {
            throw new RuntimeException('Only deployed applicants can be marked as returned home.');
        }

        $updated = $this->repository->update($applicantBatch->id, array_filter([
            'status'        => ApplicantBatchStatus::RETURNED->value,
            'returned_at'   => now(),
            'return_reason' => $dto->returnReason,
            'returned_by'   => $dto->processedBy,
            'processed_by'  => $dto->processedBy,
        ], fn ($value) => $value !== null));

        Log::info('Applicant returned home early', [
            'applicant_batch_id' => $applicantBatch->id,
            'reason'             => $dto->returnReason,
            'processed_by'       => $dto->processedBy,
        ]);

        return $updated;
    }
}