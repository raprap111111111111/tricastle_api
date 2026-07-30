<?php

namespace App\Domain\QualityScore\Actions;

use App\Domain\QualityScore\Repositories\QualityScoreRepository;
use App\Models\Applicant;
use App\Models\QualityScore;

class RecalculateQualityScoreAction
{
    public function __construct(
        private readonly QualityScoreRepository $repository
    ) {}

    public function execute(Applicant $applicant, int $calculatedBy): QualityScore
    {
        // ─── Get document stats ────────────────────────────────
        $documents = $applicant->documents()->get();

        $totalDocuments    = $documents->count();
        $verifiedDocuments = $documents->where('status', 'verified')->count();
        $rejectedDocuments = $documents->where('status', 'rejected')->count();
        $pendingDocuments  = $documents->whereIn('status', [
            'uploaded',
            'pending_verification',
            'under_review',
        ])->count();

        // ─── Get mismatch stats ────────────────────────────────
        $totalMismatches = \App\Models\VerificationMismatch::whereHas(
            'documentVerification',
            fn ($q) => $q->whereIn('applicant_document_id', $documents->pluck('id'))
        )->count();

        $criticalMismatches = \App\Models\VerificationMismatch::whereHas(
            'documentVerification',
            fn ($q) => $q->whereIn('applicant_document_id', $documents->pluck('id'))
        )->where('severity', 'critical')->count();

        // ─── Get open corrections ──────────────────────────────
        $openCorrections = \App\Models\CorrectionRequest::whereIn(
            'applicant_document_id',
            $documents->pluck('id')
        )->whereIn('status', ['pending', 'under_review', 'approved'])->count();

        // ─── Calculate component scores ────────────────────────

        // Completeness: verified / total
        $completenessScore = $totalDocuments > 0
            ? round(($verifiedDocuments / $totalDocuments) * 100, 2)
            : 0;

        // Accuracy: penalize mismatches (max 10 per doc = 0%)
        $accuracyScore = $totalDocuments > 0
            ? max(0, round(100 - (($totalMismatches / $totalDocuments) * 10), 2))
            : 0;

        // Consistency: penalize critical mismatches heavily
        $consistencyScore = $totalDocuments > 0
            ? max(0, round(100 - (($criticalMismatches / $totalDocuments) * 25), 2))
            : 100;

        // Timeliness: penalize open corrections
        $timelinessScore = $openCorrections > 0
            ? max(0, 100 - ($openCorrections * 10))
            : 100;

        // ─── Overall weighted score ────────────────────────────
        $overallScore = round(
            ($completenessScore * 0.30) +
            ($accuracyScore * 0.30)     +
            ($consistencyScore * 0.25)  +
            ($timelinessScore * 0.15),
            2
        );

        $grade = QualityScore::calculateGrade($overallScore);

        // ─── Breakdown ─────────────────────────────────────────
        $breakdown = [
            'completeness' => [
                'score'  => $completenessScore,
                'weight' => '30%',
                'detail' => "{$verifiedDocuments}/{$totalDocuments} documents verified",
            ],
            'accuracy' => [
                'score'  => $accuracyScore,
                'weight' => '30%',
                'detail' => "{$totalMismatches} total mismatches",
            ],
            'consistency' => [
                'score'  => $consistencyScore,
                'weight' => '25%',
                'detail' => "{$criticalMismatches} critical mismatches",
            ],
            'timeliness' => [
                'score'  => $timelinessScore,
                'weight' => '15%',
                'detail' => "{$openCorrections} open corrections",
            ],
        ];

        $data = [
            'overall_score'       => $overallScore,
            'grade'               => $grade,
            'completeness_score'  => $completenessScore,
            'accuracy_score'      => $accuracyScore,
            'consistency_score'   => $consistencyScore,
            'timeliness_score'    => $timelinessScore,
            'total_documents'     => $totalDocuments,
            'verified_documents'  => $verifiedDocuments,
            'rejected_documents'  => $rejectedDocuments,
            'pending_documents'   => $pendingDocuments,
            'total_mismatches'    => $totalMismatches,
            'critical_mismatches' => $criticalMismatches,
            'open_corrections'    => $openCorrections,
            'breakdown'           => $breakdown,
            'calculated_at'       => now(),
            'calculated_by'       => $calculatedBy,
        ];

        // ─── Update existing or create new ─────────────────────
        $existing = $this->repository->findByApplicant($applicant->id);

        $score = $existing
            ? $this->repository->update($existing->id, $data)
            : $this->repository->create(['applicant_id' => $applicant->id, ...$data]);

        // ─── Sync to applicant ─────────────────────────────────
        $applicant->update([
            'quality_score' => $overallScore,
            'quality_grade' => $grade,
        ]);

        return $score;
    }
}