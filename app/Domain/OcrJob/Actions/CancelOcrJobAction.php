<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\DTOs\CancelOcrJobDTO;
use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CancelOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(OcrJob $ocrJob, CancelOcrJobDTO $dto): OcrJob
    {
        $cancellableStatuses = ['pending', 'queued', 'processing', 'retrying'];

        if (!in_array($ocrJob->status, $cancellableStatuses)) {
            throw ValidationException::withMessages([
                'status' => "Job cannot be cancelled from status [{$ocrJob->status}].",
            ]);
        }

        return $this->repository->update($ocrJob->id, [
            'status'       => 'cancelled',
            'notes'        => $dto->notes,
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);
    }
}