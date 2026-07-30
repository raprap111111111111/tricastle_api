<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CorrectionRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'request_code'             => $this->request_code,
            'document_verification_id' => $this->document_verification_id,
            'applicant_document_id'    => $this->applicant_document_id,
            'severity'                 => $this->severity,
            'status'                   => $this->status,
            'description'              => $this->description,
            'fields_to_correct'        => $this->fields_to_correct,
            'correction_data'          => $this->correction_data,
            'justification'            => $this->justification,
            'requires_approval'        => $this->requires_approval,
            'requires_new_document'    => $this->requires_new_document,
            'due_date'                 => $this->due_date?->toDateTimeString(),

            // Status flags
            'is_pending'               => $this->isPending(),
            'is_under_review'          => $this->isUnderReview(),
            'is_approved'              => $this->isApproved(),
            'is_rejected'              => $this->isRejected(),
            'is_completed'             => $this->isCompleted(),
            'is_cancelled'             => $this->isCancelled(),
            'is_critical'              => $this->isCritical(),
            'is_overdue'               => $this->isOverdue(),
            'is_active'                => $this->isActive(),

            // Relations
            'document_verification'    => $this->whenLoaded('documentVerification', fn () => [
                'id'     => $this->documentVerification->id,
                'status' => $this->documentVerification->status,
            ]),
            'applicant_document'       => $this->whenLoaded('applicantDocument', fn () => [
                'id'        => $this->applicantDocument->id,
                'file_name' => $this->applicantDocument->file_name,
                'status'    => $this->applicantDocument->status,
            ]),
            'requester'                => $this->whenLoaded('requester', fn () => [
                'id'   => $this->requester->id,
                'name' => $this->requester->name,
            ]),

            'created_at'               => $this->created_at?->toDateTimeString(),
            'updated_at'               => $this->updated_at?->toDateTimeString(),
        ];
    }
}