<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'applicant_id'        => $this->applicant_id,
            'document_type_id'    => $this->document_type_id,
            'file_repository_id'  => $this->file_repository_id,

            // File Info
            'file_name'           => $this->file_name,
            'file_type'           => $this->file_type,
            'file_size'           => $this->file_size,
            'mime_type'           => $this->mime_type,

            // OCR
            'extracted_data'      => $this->extracted_data,
            'validated_data'      => $this->validated_data,
            'ocr_confidence'      => $this->ocr_confidence,

            // Status
            'status'              => $this->status,
            'priority'            => $this->priority,

            // Dates
            'document_date'       => $this->document_date?->toDateString(),
            'expiry_date'         => $this->expiry_date?->toDateString(),
            'is_expired'          => $this->is_expired,
            'expiry_notified'     => $this->expiry_notified,

            // Versioning
            'version'             => $this->version,
            'is_current_version'  => $this->is_current_version,

            // Verification
            'last_verified_at'    => $this->last_verified_at?->toDateTimeString(),

            // Rejection
            'rejection_reason'    => $this->rejection_reason,
            'rejected_at'         => $this->rejected_at?->toDateTimeString(),

            // Notes
            'notes'               => $this->notes,
            'metadata'            => $this->metadata,

            // Relations
            'applicant'           => $this->whenLoaded('applicant', fn () => [
                'id'        => $this->applicant->id,
                'full_name' => $this->applicant->full_name,
                'email'     => $this->applicant->email,
            ]),
            'document_type'       => $this->whenLoaded('documentType', fn () => [
                'id'   => $this->documentType->id,
                'name' => $this->documentType->name,
                'code' => $this->documentType->code,
            ]),
            'uploader'            => $this->whenLoaded('uploader', fn () => [
                'id'   => $this->uploader->id,
                'name' => $this->uploader->name,
            ]),
            'verifier'            => $this->whenLoaded('verifier', fn () => [
                'id'   => $this->verifier->id,
                'name' => $this->verifier->name,
            ]),
            'rejector'            => $this->whenLoaded('rejector', fn () => [
                'id'   => $this->rejector->id,
                'name' => $this->rejector->name,
            ]),

            'created_at'          => $this->created_at?->toDateTimeString(),
            'updated_at'          => $this->updated_at?->toDateTimeString(),
        ];
    }
}