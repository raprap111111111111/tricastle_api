<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\DTOs\UpdateOcrJobDTO;
use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;

class UpdateOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(OcrJob $ocrJob, UpdateOcrJobDTO $dto): OcrJob
    {
        return $this->repository->update($ocrJob->id, array_filter([
            'status_message' => $dto->statusMessage,
            'provider'       => $dto->provider,
            'provider_config' => $dto->providerConfig,
            'priority'       => $dto->priority,
            'notes'          => $dto->notes,
            'metadata'       => $dto->metadata,
            'max_attempts'   => $dto->maxAttempts,
        ], fn($value) => $value !== null));
    }
}