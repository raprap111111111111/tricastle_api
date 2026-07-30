<?php

namespace App\Domain\QualityScore\Actions;

use App\Domain\QualityScore\DTOs\UpdateQualityScoreDTO;
use App\Domain\QualityScore\Repositories\QualityScoreRepository;
use App\Models\QualityScore;

class UpdateQualityScoreAction
{
    public function __construct(
        private readonly QualityScoreRepository $repository
    ) {}

    public function execute(QualityScore $qualityScore, UpdateQualityScoreDTO $dto): QualityScore
    {
        return $this->repository->update($qualityScore->id, array_filter([
            'overall_score'       => $dto->overallScore,
            'grade'               => $dto->grade,
            'completeness_score'  => $dto->completenessScore,
            'accuracy_score'      => $dto->accuracyScore,
            'consistency_score'   => $dto->consistencyScore,
            'timeliness_score'    => $dto->timelinessScore,
            'total_documents'     => $dto->totalDocuments,
            'verified_documents'  => $dto->verifiedDocuments,
            'rejected_documents'  => $dto->rejectedDocuments,
            'pending_documents'   => $dto->pendingDocuments,
            'total_mismatches'    => $dto->totalMismatches,
            'critical_mismatches' => $dto->criticalMismatches,
            'open_corrections'    => $dto->openCorrections,
            'breakdown'           => $dto->breakdown,
            'calculated_at'       => now(),
            'calculated_by'       => $dto->calculatedBy,
        ], fn ($value) => $value !== null));
    }
}