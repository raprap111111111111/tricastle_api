<?php

// app/Domain/ApplicantEmployment/DTOs/UpdateApplicantEmploymentDTO.php

namespace App\Domain\ApplicantEmployment\DTOs;

final readonly class UpdateApplicantEmploymentDTO
{
    public function __construct(
        public ?string $companyName = null,
        public ?string $position = null,
        public ?string $industry = null,
        public ?string $jobDescription = null,
        public ?string $dateStarted = null,
        public ?string $dateEnded = null,
        public ?bool   $isCurrent = null,
        public ?string $country = null,
        public ?string $city = null,
        public ?float  $salary = null,
        public ?string $salaryCurrency = null,
        public ?string $reasonForLeaving = null,
    ) {}
}