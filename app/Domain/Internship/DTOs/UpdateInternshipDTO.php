<?php

namespace App\Domain\Internship\DTOs;

final readonly class UpdateInternshipDTO
{
    public function __construct(
        public ?int    $batchId = null,
        public ?int    $internshipProgramId = null,
        public ?string $programType = null,
        public ?string $status = null,
        public ?int    $dispatchingCompanyId = null,
        public ?int    $acceptingCompanyId = null,
        public ?int    $receivingCompanyId = null,
        public ?string $jobDescription = null,
        public ?string $placeOfInternship = null,
        public ?string $municipality = null,
        public ?string $agreementDate = null,
        public ?string $contractStart = null,
        public ?string $contractEnd = null,
        public ?int    $contractYears = null,
        public ?int    $stipendAmount = null,
        public ?int    $mealAllowanceAmount = null,
        public ?string $workDays = null,
        public ?string $dayOff = null,
        public ?string $timeStart = null,
        public ?string $timeEnd = null,
        public ?string $lunchBreak = null,
        public ?int    $updatedBy = null,
    ) {}
}