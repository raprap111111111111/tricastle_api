<?php

// app/Domain/ApplicantTattoo/DTOs/CreateApplicantTattooDTO.php

namespace App\Domain\ApplicantTattoo\DTOs;

final readonly class CreateApplicantTattooDTO
{
    public function __construct(
        public int     $applicantId,
        public string  $location,
        public ?string $size = null,
        public ?string $description = null,
        public ?string $photoPath = null,
        public bool    $isVisible = true,
    ) {}
}