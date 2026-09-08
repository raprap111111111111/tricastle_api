<?php

namespace App\Domain\Internship\DTOs;

final readonly class ChangeCompanyDTO
{
    public function __construct(
        public int     $receivingCompanyId,
        public string  $contractStart,
        public string  $changeReason,
        public ?int    $acceptingCompanyId = null,
        public ?int    $batchId = null,
        public ?int    $internshipProgramId = null,
        public ?string $contractEnd = null,
        public ?int    $contractYears = null,
        public ?string $jobDescription = null,
        public ?string $placeOfInternship = null,
        public ?int    $stipendAmount = null,
        public ?int    $mealAllowanceAmount = null,
        public ?int    $changedBy = null,
    ) {}
}