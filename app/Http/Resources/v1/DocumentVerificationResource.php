<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentVerificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'applicant_document_id' => $this->applicant_document_id,
            'status'                => $this->status,

            // Match Stats
            'match_percentage'      => $this->match_percentage,
            'total_fields'          => $this->total_fields,
            'matched_fields'        => $this->matched_fields,
            'mismatched_fields'     => $this->mismatched_fields,
            'missing_fields'        => $this->missing_fields,

            // Data
            'verification_data'     => $this->verification_data,
            'source_data'           => $this->source_data,

            // Notes
            'notes'                 => $this->notes,
            'rejection_reason'      => $this->rejection_reason,

            // Time
            'started_at'            => $this->started_at?->toDateTimeString(),
            'completed_at'          => $this->completed_at?->toDateTimeString(),
            'time_spent_seconds'    => $this->time_spent_seconds,
            'time_spent_formatted'  => $this->getTimeSpentFormatted(),

            // Status flags
            'is_pending'            => $this->isPending(),
            'is_in_progress'        => $this->isInProgress(),
            'is_completed'          => $this->isCompleted(),
            'is_approved'           => $this->isApproved(),
            'is_rejected'           => $this->isRejected(),

            // Relations
            'applicant_document'    => $this->whenLoaded('applicantDocument', fn () => [
                'id'        => $this->applicantDocument->id,
                'file_name' => $this->applicantDocument->file_name,
                'status'    => $this->applicantDocument->status,
            ]),
            'verifier'              => $this->whenLoaded('verifier', fn () => [
                'id'   => $this->verifier->id,
                'name' => $this->verifier->name,
            ]),
            'reviewer'              => $this->whenLoaded('reviewer', fn () => [
                'id'   => $this->reviewer->id,
                'name' => $this->reviewer->name,
            ]),
            'mismatches'            => $this->whenLoaded('mismatches', fn () =>
                VerificationMismatchResource::collection($this->mismatches)
            ),
            'mismatches_count'      => $this->whenLoaded('mismatches', fn () =>
                $this->mismatches->count()
            ),

            'created_at'            => $this->created_at?->toDateTimeString(),
            'updated_at'            => $this->updated_at?->toDateTimeString(),
        ];
    }
}