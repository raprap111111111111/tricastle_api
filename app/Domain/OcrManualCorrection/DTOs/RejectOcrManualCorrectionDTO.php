<?php

namespace App\Domain\OcrManualCorrection\DTOs;

final readonly class RejectOcrManualCorrectionDTO
{
    public function __construct(
        public int     $reviewedBy,
        public string  $disputeReason,
        public ?string $verificationNotes = null,
    ) {}
}