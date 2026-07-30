<?php

namespace App\Domain\Applicant\DTOs;

final readonly class CreateApplicantDTO
{
    public function __construct(
        // Personal (required)
        public string  $firstName,
        public string  $lastName,
        public string  $email,

        // Personal (optional)
        public ?string $middleName        = null,
        public ?string $suffix            = null,
        public ?string $phone             = null,
        public ?string $mobile            = null,
        public ?string $dateOfBirth       = null,
        public ?string $gender            = null,
        public ?string $civilStatus       = null,
        public ?int    $numberOfChildren  = 0,
        public ?string $nationality       = 'Filipino',

        // Physical
        public ?float  $heightCm          = null,
        public ?float  $weightKg          = null,
        public ?string $dominantHand      = null,
        public ?string $bloodType         = null,

        // Address
        public ?string $currentAddress    = null,
        public ?string $permanentAddress  = null,
        public ?string $city              = null,
        public ?string $province          = null,
        public ?string $postalCode        = null,

        // Passport / IDs
        public ?string $passportNumber    = null,
        public ?string $passportExpiry    = null,
        public ?string $sssNumber         = null,
        public ?string $tinNumber         = null,
        public ?string $philhealthNumber  = null,
        public ?string $pagibigNumber     = null,

        // Staff
        public ?int    $assignedStaffId   = null,
        public ?int    $createdBy         = null,

        // Batch (optional)
        public ?BatchAssignmentDTO $batch = null,
    ) {}
}