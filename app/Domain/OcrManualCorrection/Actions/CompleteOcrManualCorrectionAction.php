<?php

namespace App\Domain\OcrManualCorrection\Actions;

use App\Domain\OcrManualCorrection\DTOs\CompleteOcrManualCorrectionDTO;
use App\Domain\OcrManualCorrection\Repositories\OcrManualCorrectionRepository;
use App\Models\OcrManualCorrection;

class CompleteOcrManualCorrectionAction
{
    public function __construct(
        private readonly OcrManualCorrectionRepository $repository
    ) {}

    public function execute(OcrManualCorrection $correction, CompleteOcrManualCorrectionDTO $dto): OcrManualCorrection
    {
        $data = array_filter([
            'used_for_training'      => true,
            'improved_accuracy'      => $dto->improvedAccuracy,
            'accuracy_improvement'   => $dto->accuracyImprovement,
            'training_batch_id'      => $dto->trainingBatchId,
            'trained_at'             => $dto->trainedAt ?? now(),
            'is_recurring_error'     => $dto->isRecurringError,
            'occurrence_count'       => $dto->occurrenceCount,
            'similar_correction_ids' => $dto->similarCorrectionIds,
        ], fn($value) => !is_null($value));

        return $this->repository->update($correction->id, $data);
    }
}