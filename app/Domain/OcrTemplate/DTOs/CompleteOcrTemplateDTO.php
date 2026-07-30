<?php

// app/Domain/OcrTemplate/DTOs/CompleteOcrTemplateDTO.php

namespace App\Domain\OcrTemplate\DTOs;

final readonly class CompleteOcrTemplateDTO
{
    public function __construct(
        public int     $completedBy,
        public ?string $notes = null,
    ) {}
}