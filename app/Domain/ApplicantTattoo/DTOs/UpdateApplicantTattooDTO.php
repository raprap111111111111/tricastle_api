<?php

// app/Domain/ApplicantTattoo/DTOs/UpdateApplicantTattooDTO.php

namespace App\Domain\ApplicantTattoo\DTOs;

final readonly class UpdateApplicantTattooDTO
{
    public function __construct(
        public ?string $location = null,
        public ?string $size = null,
        public ?string $description = null,
        public ?string $photoPath = null,
        public ?bool   $isVisible = null,
    ) {}
}