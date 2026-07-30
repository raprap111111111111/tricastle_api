<?php

// app/Domain/ApplicantEducation/DTOs/CreateApplicantEducationDTO.php

namespace App\Domain\ApplicantEducation\DTOs;

final readonly class CreateApplicantEducationDTO
{
    public function __construct(
        public int     $applicantId,
        public string  $educationLevel,
        public string  $schoolName,
        public string  $educationStatus = 'graduate',
        public ?string $course = null,
        public ?int    $yearStarted = null,
        public ?int    $yearEnded = null,
        public ?string $honors = null,
    ) {}
}