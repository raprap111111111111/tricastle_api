<?php

namespace App\Domain\OcrManualCorrection\DTOs;

final readonly class UpdateOcrManualCorrectionDTO
{
    public function __construct(
        public ?string $correctedValue = null,
        public ?string $reason = null,
        public ?string $explanation = null,
        public ?string $correctionType = null,
        public ?string $severity = null,
        public ?float  $confidenceBefore = null,
        public ?float  $confidenceAfter = null,
        public ?int    $charactersChanged = null,
        public ?float  $similarityScore = null,
        public ?int    $editDistance = null,
        public ?bool   $wasCriticalField = null,
        public ?string $providerUsed = null,
        public ?string $templateUsed = null,
        public ?array  $imageMetadata = null,
        public ?array  $surroundingText = null,
        public ?array  $fieldPosition = null,
        public ?int    $timeToCorrectSeconds = null,
        public ?string $correctionStartedAt = null,
        public ?string $correctionCompletedAt = null,
        public ?string $notes = null,
        public ?array  $metadata = null,
        public ?array  $tags = null,
    ) {}
}