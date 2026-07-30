<?php

// app/Domain/ApplicantBatch/DTOs/RecordExamResultDTO.php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class RecordExamResultDTO
{
    public function __construct(
        public string  $examDate,
        public float   $examScore,
        public bool    $passed,
        public ?int    $processedBy = null,
    ) {}
}