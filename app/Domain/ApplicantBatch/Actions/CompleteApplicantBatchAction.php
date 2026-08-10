<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Enums\ApplicantBatchStatus;
use App\Domain\ApplicantBatch\DTOs\CompleteApplicantBatchDTO;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CompleteApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, CompleteApplicantBatchDTO $dto): ApplicantBatch
    {
        if ($applicantBatch->status !== ApplicantBatchStatus::DEPLOYED) {
            throw new RuntimeException('Only deployed applicants can be marked as completed.');
        }

        $updated = $this->repository->update($applicantBatch->id, array_filter([
            'status'           => ApplicantBatchStatus::COMPLETED->value,
            'completed_at'     => now(),
            'completion_notes' => $dto->completionNotes,
            'completed_by'     => $dto->processedBy,
            'processed_by'     => $dto->processedBy,
        ], fn ($value) => $value !== null));

        Log::info('Applicant contract completed', [
            'applicant_batch_id' => $applicantBatch->id,
            'processed_by'       => $dto->processedBy,
        ]);

        return $updated;
    }
}