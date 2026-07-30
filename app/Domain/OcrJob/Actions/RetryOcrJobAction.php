<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\DTOs\RetryOcrJobDTO;
use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;
use Illuminate\Validation\ValidationException;

class RetryOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(OcrJob $ocrJob, RetryOcrJobDTO $dto): OcrJob
    {
        $retryableStatuses = ['failed', 'timeout', 'cancelled'];

        if (!in_array($ocrJob->status, $retryableStatuses)) {
            throw ValidationException::withMessages([
                'status' => "Job cannot be retried from status [{$ocrJob->status}].",
            ]);
        }

        if ($ocrJob->attempt_number >= $ocrJob->max_attempts) {
            throw ValidationException::withMessages([
                'attempt_number' => 'Job has reached maximum retry attempts.',
            ]);
        }

        return $this->repository->update($ocrJob->id, array_filter([
            'status'           => 'retrying',
            'attempt_number'   => $ocrJob->attempt_number + 1,
            'provider'         => $dto->provider,
            'priority'         => $dto->priority,
            'notes'            => $dto->notes,
            'error_message'    => null,
            'error_code'       => null,
            'failed_at'        => null,
            'retry_at'         => now(),
        ], fn($v) => $v !== null));
    }
}