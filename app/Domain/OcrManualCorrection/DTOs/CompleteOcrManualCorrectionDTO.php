<?php

namespace App\Domain\OcrManualCorrection\DTOs;

final readonly class CompleteOcrManualCorrectionDTO
{
    public function __construct(
        public bool    $improvedAccuracy = false,
        public ?float  $accuracyImprovement = null,
        public ?string $trainingBatchId = null,
        public ?string $trainedAt = null,
        public bool    $isRecurringError = false,
        public int     $occurrenceCount = 1,
        public ?array  $similarCorrectionIds = null,
    ) {}
}