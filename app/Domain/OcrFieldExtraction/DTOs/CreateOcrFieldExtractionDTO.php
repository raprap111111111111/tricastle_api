<?php

namespace App\Domain\OcrFieldExtraction\DTOs;

final readonly class CreateOcrFieldExtractionDTO
{
    public function __construct(
        public int     $ocrJobId,
        public int     $applicantDocumentId,
        public string  $fieldName,
        public string  $fieldLabel,
        public string  $fieldType,
        public ?string $fieldCategory = null,
        public bool    $isRequired = false,
        public bool    $isPrimaryField = false,
        public int     $sortOrder = 0,
        public ?string $extractedValue = null,
        public ?string $normalizedValue = null,
        public float   $confidenceScore = 0,
        public string  $confidenceLevel = 'unknown',
        public ?float  $characterConfidence = null,
        public ?float  $wordConfidence = null,
        public ?int    $characterCount = null,
        public ?int    $wordCount = null,
        public ?array  $boundingBox = null,
        public int     $pageNumber = 1,
        public ?float  $xCoordinate = null,
        public ?float  $yCoordinate = null,
        public ?float  $width = null,
        public ?float  $height = null,
        public ?float  $rotationAngle = null,
        public string  $status = 'extracted',
        public string  $source = 'ocr',
        public ?string $notes = null,
        public ?array  $metadata = null,
    ) {}
}