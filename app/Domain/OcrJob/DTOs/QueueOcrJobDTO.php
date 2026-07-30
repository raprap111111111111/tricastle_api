<?php

namespace App\Domain\OcrJob\DTOs;

final readonly class QueueOcrJobDTO
{
    public function __construct(
        public int    $ocrJobId,
        public int    $priority = 5,
    ) {}
}