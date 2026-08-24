<?php

namespace App\Domain\Applicant\DTOs;

final readonly class UpdateApplicantDTO
{
    public function __construct(
        // ── Personal ──────────────────────────────────────────────────
        public ?string $firstName         = null,
        public ?string $middleName        = null,
        public ?string $lastName          = null,
        public ?string $suffix            = null,
        public ?string $email             = null,
        public ?string $phone             = null,
        public ?string $mobile            = null,
        public ?string $dateOfBirth       = null,
        public ?string $gender            = null,
        public ?string $civilStatus       = null,
        public ?int    $numberOfChildren  = null,
        public ?string $nationality       = null,
        
        // ── AIS / Trade Test (NEW) ────────────────────────────────────
        public ?string $appliedPosition       = null,
        public ?string $tradeTestTry          = null,
        public ?string $tradeTestDate         = null,
        public ?string $birthplace            = null,
        public ?string $religion              = null,
        public ?int    $englishProficiencyPct = null,

        // ── Physical ──────────────────────────────────────────────────
        public ?float  $heightCm          = null,
        public ?float  $weightKg          = null,
        public ?string $dominantHand      = null,
        public ?string $bloodType         = null,

        // ── Address ───────────────────────────────────────────────────
        public ?string $currentAddress    = null,
        public ?string $permanentAddress  = null,
        public ?string $city              = null,
        public ?string $province          = null,
        public ?string $postalCode        = null,

        // ── Passport / IDs ────────────────────────────────────────────
        public ?string $passportNumber    = null,
        public ?string $passportExpiry    = null,
        public ?string $sssNumber         = null,
        public ?string $tinNumber         = null,
        public ?string $philhealthNumber  = null,
        public ?string $pagibigNumber     = null,

        // ── Skill / Trade ─────────────────────────────────────────────
        public ?string $skillCategory     = null,
        public ?string $tradeOrOccupation = null,

        // ── Language ──────────────────────────────────────────────────
        public ?bool   $understandsBasicEnglish = null,
        public ?string $jlptLevel               = null,

        // ── Japan Deployment ──────────────────────────────────────────
        public ?bool   $willingToBeDeployed   = null,
        public ?bool   $japanDeploymentReady  = null,
        public ?string $preferredWorkLocation = null,

        // ── Japan Experience ──────────────────────────────────────────
        public ?bool   $previousJapanExperience = null,
        public ?int    $yearsJapanExperience    = null,

        // ── Certifications ────────────────────────────────────────────
        public ?bool   $hasTitpCertificate = null,
        public ?string $titpOccupation     = null,
        public ?bool   $sswEligible        = null,

        // ── Salary ────────────────────────────────────────────────────
        public ?float  $expectedSalary         = null,
        public ?string $expectedSalaryCurrency = null,
        public ?float  $currentSalary          = null,
        public ?string $currentSalaryCurrency  = null,

        // ── Family (Updated with AIS extras) ──────────────────────────
        public ?string $fatherName         = null,
        public ?string $fatherOccupation   = null,
        public ?string $fatherContact      = null,
        public ?string $motherName         = null,
        public ?string $motherOccupation   = null,
        public ?string $motherContact      = null,
        public ?string $spouseName         = null,
        public ?string $spouseOccupation   = null,
        public ?string $spouseContact      = null,
        public ?float  $spouseSalary       = null,
        public ?string $spouseSalaryUnit   = null,

        // ── Japan Contacts (NEW array) ────────────────────────────────
        public ?array  $japanContacts      = null,

        // ── Emergency Contact ─────────────────────────────────────────
        public ?string $emergencyContactName         = null,
        public ?string $emergencyContactRelationship = null,
        public ?string $emergencyContactPhone        = null,
        public ?string $emergencyContactAddress      = null,

        // ── Status ────────────────────────────────────────────────────
        public ?string $status          = null,
        public ?string $rejectionReason = null,

        // ── Quality ───────────────────────────────────────────────────
        public ?float  $qualityScore    = null,
        public ?string $qualityGrade    = null,

        // ── Staff ─────────────────────────────────────────────────────
        public ?int    $assignedStaffId = null,
        public ?int    $reviewedBy      = null,
    ) {}
}