<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'applicant_id'        => $this->applicant_id,
            'overall_score'       => $this->overall_score,
            'grade'               => $this->grade,

            // Component scores
            'completeness_score'  => $this->completeness_score,
            'accuracy_score'      => $this->accuracy_score,
            'consistency_score'   => $this->consistency_score,
            'timeliness_score'    => $this->timeliness_score,

            // Document stats
            'total_documents'     => $this->total_documents,
            'verified_documents'  => $this->verified_documents,
            'rejected_documents'  => $this->rejected_documents,
            'pending_documents'   => $this->pending_documents,

            // Issue stats
            'total_mismatches'    => $this->total_mismatches,
            'critical_mismatches' => $this->critical_mismatches,
            'open_corrections'    => $this->open_corrections,

            // Computed
            'verification_rate'   => $this->getVerificationRate(),
            'rejection_rate'      => $this->getRejectionRate(),

            // Breakdown
            'breakdown'           => $this->breakdown,

            // Status flags
            'is_passing'          => $this->isPassingGrade(),
            'is_failing'          => $this->isFailingGrade(),
            'has_critical_issues' => $this->hasCriticalIssues(),

            // Calculated
            'calculated_at'       => $this->calculated_at?->toDateTimeString(),

            // Relations
            'applicant'           => $this->whenLoaded('applicant', fn () => [
                'id'        => $this->applicant->id,
                'full_name' => $this->applicant->full_name,
                'email'     => $this->applicant->email,
            ]),
            'calculator'          => $this->whenLoaded('calculator', fn () => [
                'id'   => $this->calculator->id,
                'name' => $this->calculator->name,
            ]),

            'created_at'          => $this->created_at?->toDateTimeString(),
            'updated_at'          => $this->updated_at?->toDateTimeString(),
        ];
    }
}