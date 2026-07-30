<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\DTOs\QueueOcrJobDTO;
use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;
use Illuminate\Validation\ValidationException;

class QueueOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(OcrJob $ocrJob, QueueOcrJobDTO $dto): OcrJob
    {
        if (!in_array($ocrJob->status, ['pending', 'failed', 'timeout'])) {
            throw ValidationException::withMessages([
                'status' => "Job cannot be queued from status [{$ocrJob->status}].",
            ]);
        }

        return $this->repository->update($ocrJob->id, [
            'status'    => 'queued',
            'priority'  => $dto->priority,
            'queued_at' => now(),
        ]);
    }
}