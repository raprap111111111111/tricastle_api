<?php

namespace App\Domain\QualityScore\DTOs;

final readonly class UpdateQualityScoreDTO
{
    public function __construct(
        public ?float  $overallScore       = null,
        public ?string $grade              = null,
        public ?float  $completenessScore  = null,
        public ?float  $accuracyScore      = null,
        public ?float  $consistencyScore   = null,
        public ?float  $timelinessScore    = null,
        public ?int    $totalDocuments     = null,
        public ?int    $verifiedDocuments  = null,
        public ?int    $rejectedDocuments  = null,
        public ?int    $pendingDocuments   = null,
        public ?int    $totalMismatches    = null,
        public ?int    $criticalMismatches = null,
        public ?int    $openCorrections    = null,
        public ?array  $breakdown          = null,
        public ?int    $calculatedBy       = null,
    ) {}
}