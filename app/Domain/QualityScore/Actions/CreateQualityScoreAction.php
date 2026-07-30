<?php

namespace App\Domain\QualityScore\Actions;

use App\Domain\QualityScore\DTOs\CreateQualityScoreDTO;
use App\Domain\QualityScore\Notifications\QualityScoreGeneratedNotification;
use App\Domain\QualityScore\Repositories\QualityScoreRepository;
use App\Models\QualityScore;
use App\Models\User;

class CreateQualityScoreAction
{
    public function __construct(
        private readonly QualityScoreRepository $repository
    ) {}

    public function execute(CreateQualityScoreDTO $dto): QualityScore
    {
        // ─── Create quality score ──────────────────────────────
        $score = $this->repository->create([
            'applicant_id'        => $dto->applicantId,
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
        ]);

        // ─── Notify only on low score or critical mismatches ───
        if ($dto->criticalMismatches > 0 || $dto->overallScore < 60) {
            User::permission('quality-score.viewAny')
                ->get()
                ->each(fn(User $user) => $user->notify(
                    new QualityScoreGeneratedNotification($score)
                ));
        }

        return $score;
    }
}