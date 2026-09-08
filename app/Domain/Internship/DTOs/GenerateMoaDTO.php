<?php

namespace App\Domain\Internship\DTOs;

final readonly class GenerateMoaDTO
{
    public function __construct(
        public ?string $agreementDate = null,
        public ?string $municipality = null,
        public ?string $contractStart = null,
        public ?string $contractEnd = null,
        public ?int    $contractYears = null,
        public ?string $jobDescription = null,
        public ?string $placeOfInternship = null,
        public ?int    $stipendAmount = null,
        public ?int    $mealAllowanceAmount = null,
        public ?int    $generatedBy = null,
    ) {}
}