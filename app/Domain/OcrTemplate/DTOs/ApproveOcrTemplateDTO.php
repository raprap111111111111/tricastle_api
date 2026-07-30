<?php

// app/Domain/OcrTemplate/DTOs/ApproveOcrTemplateDTO.php

namespace App\Domain\OcrTemplate\DTOs;

final readonly class ApproveOcrTemplateDTO
{
    public function __construct(
        public int     $approvedBy,
        public ?string $notes = null,
    ) {}
}