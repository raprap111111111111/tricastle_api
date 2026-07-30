<?php

namespace App\Domain\QualityScore\DTOs;

final readonly class CreateQualityScoreDTO
{
    public function __construct(
        public int    $applicantId,
        public float  $overallScore,
        public string $grade,
        public float  $completenessScore  = 0,
        public float  $accuracyScore      = 0,
        public float  $consistencyScore   = 0,
        public float  $timelinessScore    = 0,
        public int    $totalDocuments     = 0,
        public int    $verifiedDocuments  = 0,
        public int    $rejectedDocuments  = 0,
        public int    $pendingDocuments   = 0,
        public int    $totalMismatches    = 0,
        public int    $criticalMismatches = 0,
        public int    $openCorrections    = 0,
        public ?array $breakdown          = null,
        public ?int   $calculatedBy       = null,
    ) {}
}