<?php

// app/Domain/ApplicantBatch/DTOs/ScheduleInterviewDTO.php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class ScheduleInterviewDTO
{
    public function __construct(
        public string  $interviewDate,
        public ?string $interviewNotes = null,
        public ?int    $processedBy = null,
    ) {}
}