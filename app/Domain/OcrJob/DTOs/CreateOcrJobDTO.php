<?php

namespace App\Domain\OcrJob\DTOs;

final readonly class CreateOcrJobDTO
{
    public function __construct(
        public int     $applicantDocumentId,
        public ?int    $fileRepositoryId = null,
        public ?int    $ocrTemplateId = null,
        public ?string $batchId = null,
        public string  $provider = 'tesseract',
        public ?array  $providerConfig = null,
        public int     $priority = 5,
        public ?string $notes = null,
        public ?array  $metadata = null,
    ) {}
}