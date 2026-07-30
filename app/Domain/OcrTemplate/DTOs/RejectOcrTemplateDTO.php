<?php

// app/Domain/OcrTemplate/DTOs/RejectOcrTemplateDTO.php

namespace App\Domain\OcrTemplate\DTOs;

final readonly class RejectOcrTemplateDTO
{
    public function __construct(
        public int     $rejectedBy,
        public string  $reason,
        public ?string $notes = null,
    ) {}
}