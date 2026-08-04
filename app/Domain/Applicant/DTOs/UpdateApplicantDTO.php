<?php

namespace App\Domain\Applicant\DTOs;

final readonly class UpdateApplicantDTO
{
    public function __construct(
        public ?string $firstName        = null,
        public ?string $middleName       = null,
        public ?string $lastName         = null,
        public ?string $suffix           = null,
        public ?string $email            = null,
        public ?string $phone            = null,
        public ?string $mobile           = null,
        public ?string $dateOfBirth      = null,
        public ?string $gender           = null,
        public ?string $civilStatus      = null,
        public ?int    $numberOfChildren = null,
        public ?string $nationality      = null,

        public ?float  $heightCm         = null,
        public ?float  $weightKg         = null,
        public ?string $dominantHand     = null,
        public ?string $bloodType        = null,

        public ?string $currentAddress   = null,
        public ?string $permanentAddress = null,
        public ?string $city             = null,
        public ?string $province         = null,
        public ?string $postalCode       = null,

        public ?string $passportNumber   = null,
        public ?string $passportExpiry   = null,
        public ?string $sssNumber        = null,
        public ?string $tinNumber        = null,
        public ?string $philhealthNumber = null,
        public ?string $pagibigNumber    = null,

        // ─── Status ──────────────────────────────────
        public ?string $status           = null,
        public ?string $rejectionReason  = null,

        // ─── Quality ─────────────────────────────────
        public ?float  $qualityScore     = null,
        public ?string $qualityGrade     = null,

        // ─── Staff ───────────────────────────────────
        public ?int    $assignedStaffId  = null,
        public ?int    $reviewedBy       = null,
    ) {}
}