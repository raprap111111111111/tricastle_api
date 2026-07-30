<?php

// app/Domain/ApplicantEmployment/DTOs/CreateApplicantEmploymentDTO.php

namespace App\Domain\ApplicantEmployment\DTOs;

final readonly class CreateApplicantEmploymentDTO
{
    public function __construct(
        public int     $applicantId,
        public string  $companyName,
        public string  $position,
        public string  $dateStarted,
        public ?string $industry = null,
        public ?string $jobDescription = null,
        public ?string $dateEnded = null,
        public bool    $isCurrent = false,
        public string  $country = 'Philippines',
        public ?string $city = null,
        public ?float  $salary = null,
        public string  $salaryCurrency = 'PHP',
        public ?string $reasonForLeaving = null,
    ) {}
}