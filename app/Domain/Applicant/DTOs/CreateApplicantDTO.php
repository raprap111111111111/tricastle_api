<?php

namespace App\Domain\Applicant\DTOs;

final readonly class CreateApplicantDTO
{
    public function __construct(
        // ── Personal (required) ───────────────────────────────────────────
        public string  $firstName,
        public string  $lastName,
        public string  $email,

        // ── Personal (optional) ───────────────────────────────────────────
        public ?string $middleName        = null,
        public ?string $suffix            = null,
        public ?string $phone             = null,
        public ?string $mobile            = null,
        public ?string $dateOfBirth       = null,
        public ?string $gender            = null,
        public ?string $civilStatus       = null,
        public ?int    $numberOfChildren  = 0,
        public ?string $nationality       = 'Filipino',

        // ── Physical ──────────────────────────────────────────────────────
        public ?float  $heightCm          = null,
        public ?float  $weightKg          = null,
        public ?string $dominantHand      = null,
        public ?string $bloodType         = null,

        // ── Address ───────────────────────────────────────────────────────
        public ?string $currentAddress    = null,
        public ?string $permanentAddress  = null,
        public ?string $city              = null,
        public ?string $province          = null,
        public ?string $postalCode        = null,

        // ── Passport / IDs ────────────────────────────────────────────────
        public ?string $passportNumber    = null,
        public ?string $passportExpiry    = null,
        public ?string $sssNumber         = null,
        public ?string $tinNumber         = null,
        public ?string $philhealthNumber  = null,
        public ?string $pagibigNumber     = null,

        // ── Skill / Trade ─────────────────────────────────────────────────
        public ?string $skillCategory            = null,  // skilled|semi_skilled|unskilled
        public ?string $tradeOrOccupation        = null,  // e.g. "Formwork Carpenter"

        // ── Language ──────────────────────────────────────────────────────
        public bool    $understandsBasicEnglish  = false,
        public ?string $jlptLevel                = null,  // N5|N4|N3|N2|N1

        // ── Japan Deployment Readiness ────────────────────────────────────
        public bool    $willingToBeDeployed      = false,
        public bool    $japanDeploymentReady     = false,
        public ?string $preferredWorkLocation    = null,

        // ── Prior Japan Experience ────────────────────────────────────────
        public bool    $previousJapanExperience  = false,
        public int     $yearsJapanExperience     = 0,

        // ── TITP / SSW Certifications ─────────────────────────────────────
        public bool    $hasTitpCertificate       = false,
        public ?string $titpOccupation           = null,
        public bool    $sswEligible              = false,

        // ── Salary ────────────────────────────────────────────────────────
        public ?float  $expectedSalary           = null,
        public string  $expectedSalaryCurrency   = 'JPY',
        public ?float  $currentSalary            = null,
        public string  $currentSalaryCurrency    = 'PHP',

        // ── Family ────────────────────────────────────────────────────────
        public ?string $fatherName               = null,
        public ?string $fatherOccupation         = null,
        public ?string $fatherContact            = null,
        public ?string $motherName               = null,
        public ?string $motherOccupation         = null,
        public ?string $motherContact            = null,
        public ?string $spouseName               = null,
        public ?string $spouseOccupation         = null,
        public ?string $spouseContact            = null,

        // ── Emergency Contact ─────────────────────────────────────────────
        public ?string $emergencyContactName         = null,
        public ?string $emergencyContactRelationship = null,
        public ?string $emergencyContactPhone        = null,
        public ?string $emergencyContactAddress      = null,

        // ── Staff ─────────────────────────────────────────────────────────
        public ?int    $assignedStaffId   = null,
        public ?int    $createdBy         = null,
    ) {}
}