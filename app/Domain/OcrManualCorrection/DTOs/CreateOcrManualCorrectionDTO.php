<?php

namespace App\Domain\OcrManualCorrection\DTOs;

final readonly class CreateOcrManualCorrectionDTO
{
    public function __construct(
        // Relationships
        public int     $ocrJobId,
        public int     $ocrFieldExtractionId,
        public int     $applicantDocumentId,
        public ?int    $ocrTemplateId = null,

        // Field Information
        public string  $fieldName,
        public string  $fieldLabel,
        public string  $fieldType,

        // Correction Details
        public string  $correctedValue,
        public ?string $originalValue = null,
        public ?string $previousCorrection = null,
        public ?string $reason = null,
        public ?string $explanation = null,

        // Classification
        public ?string $correctionType = null,
        public string  $severity = 'minor',

        // Impact Metrics
        public ?float  $confidenceBefore = null,
        public ?float  $confidenceAfter = null,
        public ?int    $charactersChanged = null,
        public ?float  $similarityScore = null,
        public ?int    $editDistance = null,
        public bool    $wasCriticalField = false,

        // User Info
        public int     $correctedBy,

        // Context
        public ?string $providerUsed = null,
        public ?string $templateUsed = null,
        public ?array  $imageMetadata = null,
        public ?array  $surroundingText = null,
        public ?array  $fieldPosition = null,

        // Timing
        public ?int    $timeToCorrectSeconds = null,
        public ?string $correctionStartedAt = null,
        public ?string $correctionCompletedAt = null,

        // Additional
        public ?string $notes = null,
        public ?array  $metadata = null,
        public ?array  $tags = null,
    ) {}
}