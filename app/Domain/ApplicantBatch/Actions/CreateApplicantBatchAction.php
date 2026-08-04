<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\CreateApplicantBatchDTO;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;
use Illuminate\Support\Facades\Log;

class CreateApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(CreateApplicantBatchDTO $dto): ApplicantBatch
    {
        $record = $this->repository->create([
            'applicant_id' => $dto->applicantId,
            'batch_id'     => $dto->batchId,
            'status'       => 'assigned',       // Always starts as assigned
            'assigned_at'  => now(),
            'processed_by' => $dto->processedBy,
        ]);

        Log::info('Applicant assigned to batch', [
            'applicant_id' => $dto->applicantId,
            'batch_id'     => $dto->batchId,
            'processed_by' => $dto->processedBy,
        ]);

        return $record;
    }
}