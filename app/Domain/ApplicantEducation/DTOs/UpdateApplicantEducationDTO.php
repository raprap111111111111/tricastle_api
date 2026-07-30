<?php

// app/Domain/ApplicantEducation/DTOs/UpdateApplicantEducationDTO.php

namespace App\Domain\ApplicantEducation\DTOs;

final readonly class UpdateApplicantEducationDTO
{
    public function __construct(
        public ?string $educationLevel = null,
        public ?string $educationStatus = null,
        public ?string $schoolName = null,
        public ?string $course = null,
        public ?int    $yearStarted = null,
        public ?int    $yearEnded = null,
        public ?string $honors = null,
    ) {}
}