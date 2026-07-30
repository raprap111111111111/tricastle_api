<?php

namespace App\Domain\QualityScore\Mappers;

use App\Domain\QualityScore\DTOs\CreateQualityScoreDTO;
use App\Domain\QualityScore\DTOs\UpdateQualityScoreDTO;
use App\Http\Requests\v1\QualityScore\StoreQualityScoreRequest;
use App\Http\Requests\v1\QualityScore\UpdateQualityScoreRequest;
use App\Models\QualityScore;

class QualityScoreMapper
{
    public static function fromCreateRequest(StoreQualityScoreRequest $request): CreateQualityScoreDTO
    {
        $overallScore = (float) $request->validated('overall_score');
        $grade        = QualityScore::calculateGrade($overallScore);

        return new CreateQualityScoreDTO(
            applicantId:        (int) $request->validated('applicant_id'),
            overallScore:       $overallScore,
            grade:              $grade,
            completenessScore:  (float) $request->validated('completeness_score', 0),
            accuracyScore:      (float) $request->validated('accuracy_score', 0),
            consistencyScore:   (float) $request->validated('consistency_score', 0),
            timelinessScore:    (float) $request->validated('timeliness_score', 0),
            totalDocuments:     (int) $request->validated('total_documents', 0),
            verifiedDocuments:  (int) $request->validated('verified_documents', 0),
            rejectedDocuments:  (int) $request->validated('rejected_documents', 0),
            pendingDocuments:   (int) $request->validated('pending_documents', 0),
            totalMismatches:    (int) $request->validated('total_mismatches', 0),
            criticalMismatches: (int) $request->validated('critical_mismatches', 0),
            openCorrections:    (int) $request->validated('open_corrections', 0),
            breakdown:          $request->validated('breakdown'),
            calculatedBy:       $request->user()->id,
        );
    }

    public static function fromUpdateRequest(UpdateQualityScoreRequest $request): UpdateQualityScoreDTO
    {
        $overallScore = $request->has('overall_score')
            ? (float) $request->validated('overall_score')
            : null;

        $grade = $overallScore !== null
            ? QualityScore::calculateGrade($overallScore)
            : null;

        return new UpdateQualityScoreDTO(
            overallScore:       $overallScore,
            grade:              $grade,
            completenessScore:  $request->has('completeness_score') ? (float) $request->validated('completeness_score') : null,
            accuracyScore:      $request->has('accuracy_score') ? (float) $request->validated('accuracy_score') : null,
            consistencyScore:   $request->has('consistency_score') ? (float) $request->validated('consistency_score') : null,
            timelinessScore:    $request->has('timeliness_score') ? (float) $request->validated('timeliness_score') : null,
            totalDocuments:     $request->has('total_documents') ? (int) $request->validated('total_documents') : null,
            verifiedDocuments:  $request->has('verified_documents') ? (int) $request->validated('verified_documents') : null,
            rejectedDocuments:  $request->has('rejected_documents') ? (int) $request->validated('rejected_documents') : null,
            pendingDocuments:   $request->has('pending_documents') ? (int) $request->validated('pending_documents') : null,
            totalMismatches:    $request->has('total_mismatches') ? (int) $request->validated('total_mismatches') : null,
            criticalMismatches: $request->has('critical_mismatches') ? (int) $request->validated('critical_mismatches') : null,
            openCorrections:    $request->has('open_corrections') ? (int) $request->validated('open_corrections') : null,
            breakdown:          $request->validated('breakdown'),
            calculatedBy:       $request->user()->id,
        );
    }
}