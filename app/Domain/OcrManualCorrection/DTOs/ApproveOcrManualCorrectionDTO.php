<?php

namespace App\Domain\OcrManualCorrection\DTOs;

final readonly class ApproveOcrManualCorrectionDTO
{
    public function __construct(
        public int     $verifiedBy,
        public ?string $verificationNotes = null,
        public bool    $usedForTraining = false,
        public bool    $addedToPatternLibrary = false,
        public ?string $errorPatternId = null,
    ) {}
}