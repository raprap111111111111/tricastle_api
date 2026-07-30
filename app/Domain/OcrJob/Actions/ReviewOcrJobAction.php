<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\DTOs\ReviewOcrJobDTO;
use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(OcrJob $ocrJob, ReviewOcrJobDTO $dto): OcrJob
    {
        if ($ocrJob->status !== 'requires_review') {
            throw ValidationException::withMessages([
                'status' => "Only jobs with status [requires_review] can be reviewed.",
            ]);
        }

        $allowedStatuses = ['completed', 'requires_review', 'failed'];

        if (!in_array($dto->status, $allowedStatuses)) {
            throw ValidationException::withMessages([
                'status' => "Review must resolve to: " . implode(', ', $allowedStatuses),
            ]);
        }

        return $this->repository->update($ocrJob->id, [
            'status'      => $dto->status,
            'notes'       => $dto->notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }
}