<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerificationMismatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'document_verification_id' => $this->document_verification_id,
            'field_name'               => $this->field_name,
            'field_label'              => $this->field_label,
            'source_value'             => $this->source_value,
            'entered_value'            => $this->entered_value,
            'severity'                 => $this->severity,
            'mismatch_type'            => $this->mismatch_type,
            'status'                   => $this->status,
            'resolution_notes'         => $this->resolution_notes,
            'is_resolved'              => $this->isResolved(),
            'is_critical'              => $this->isCritical(),
            'is_escalated'             => $this->isEscalated(),
            'resolved_at'              => $this->resolved_at?->toDateTimeString(),
            'document_verification'    => $this->whenLoaded('documentVerification', fn () => [
                'id' => $this->documentVerification->id,
            ]),
            'resolver'                 => $this->whenLoaded('resolver', fn () => [
                'id'   => $this->resolver->id,
                'name' => $this->resolver->name,
            ]),
            'created_at'               => $this->created_at?->toDateTimeString(),
            'updated_at'               => $this->updated_at?->toDateTimeString(),
        ];
    }
}