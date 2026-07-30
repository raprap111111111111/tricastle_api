<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,

            'applicant_id'     => $this->applicant_id,
            'applicant'        => $this->whenLoaded('applicant', function () {
                return [
                    'id'   => $this->applicant->id,
                    'name' => $this->applicant->name ?? null,
                ];
            }),

            'batch_id'         => $this->batch_id,
            'batch'            => $this->whenLoaded('batch', function () {
                return [
                    'id'         => $this->batch->id,
                    'batch_code' => $this->batch->batch_code,
                    'name'       => $this->batch->name,
                ];
            }),

            'status'           => $this->status?->value,
            'status_label'     => $this->status?->label(),
            'is_terminal'      => $this->status?->isTerminal() ?? false,

            'applied_at'       => $this->applied_at?->toDateString(),
            'interview_date'   => $this->interview_date?->toDateString(),
            'medical_date'     => $this->medical_date?->toDateString(),
            'exam_date'        => $this->exam_date?->toDateString(),
            'accepted_at'      => $this->accepted_at?->toDateString(),
            'deployed_at'      => $this->deployed_at?->toDateString(),

            'exam_score'       => $this->exam_score !== null ? (float) $this->exam_score : null,
            'interview_notes'  => $this->interview_notes,
            'medical_notes'    => $this->medical_notes,
            'rejection_reason' => $this->rejection_reason,

            'processed_by'     => $this->processed_by,
            'processed_by_user' => $this->whenLoaded('processedBy', function () {
                return [
                    'id'   => $this->processedBy->id,
                    'name' => $this->processedBy->name ?? null,
                ];
            }),

            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
            'deleted_at'       => $this->whenNotNull($this->deleted_at?->toIso8601String()),
        ];
    }
}