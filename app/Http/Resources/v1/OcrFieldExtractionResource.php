<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OcrFieldExtractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Identity
            'id'                    => $this->id,
            'ocr_job_id'            => $this->ocr_job_id,
            'applicant_document_id' => $this->applicant_document_id,

            // Field Definition
            'field' => [
                'name'             => $this->field_name,
                'label'            => $this->field_label,
                'type'             => $this->field_type,
                'category'         => $this->field_category,
                'is_required'      => $this->is_required,
                'is_primary_field' => $this->is_primary_field,
                'sort_order'       => $this->sort_order,
            ],

            // Values (progressive pipeline)
            'values' => [
                'extracted'   => $this->extracted_value,
                'normalized'  => $this->normalized_value,
                'validated'   => $this->validated_value,
                'final'       => $this->final_value,
                'display'     => $this->display_value,
            ],

            // Confidence
            'confidence' => [
                'score'               => $this->confidence_score,
                'level'               => $this->confidence_level,
                'character_confidence' => $this->character_confidence,
                'word_confidence'     => $this->word_confidence,
                'character_count'     => $this->character_count,
                'word_count'          => $this->word_count,
            ],

            // Location on Document
            'location' => [
                'page_number'    => $this->page_number,
                'bounding_box'   => $this->bounding_box,
                'x_coordinate'   => $this->x_coordinate,
                'y_coordinate'   => $this->y_coordinate,
                'width'          => $this->width,
                'height'         => $this->height,
                'rotation_angle' => $this->rotation_angle,
            ],

            // Validation
            'validation' => [
                'passed'           => $this->passed_validation,
                'has_errors'       => $this->has_validation_errors,
                'errors'           => $this->validation_errors,
                'rule_used'        => $this->validation_rule_used,
                'details'          => $this->validation_details,
            ],

            // Status
            'status' => $this->status,
            'source' => $this->source,

            // Correction History
            'correction' => [
                'was_corrected'    => $this->was_manually_corrected,
                'original_value'   => $this->original_ocr_value,
                'reason'           => $this->correction_reason,
                'correction_count' => $this->correction_count,
                'corrected_at'     => $this->corrected_at?->toISOString(),
                'corrected_by'     => $this->when(
                    $this->relationLoaded('corrector'),
                    fn() => $this->corrector?->only('id', 'name', 'email')
                ),
            ],

            // Cross-Reference
            'cross_reference' => [
                'matches_other_documents' => $this->matches_other_documents,
                'cross_reference_matches' => $this->cross_reference_matches,
                'has_conflicts'           => $this->has_conflicts,
                'conflict_details'        => $this->conflict_details,
            ],

            // AI / ML
            'ai_suggestions' => [
                'alternatives'       => $this->suggested_alternatives,
                'spell_check_score'  => $this->spell_check_score,
                'has_typo_suggestions' => $this->has_typo_suggestions,
            ],

            // Meta
            'notes'    => $this->notes,
            'metadata' => $this->metadata,

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'ocr_job'            => $this->whenLoaded('ocrJob'),
            'applicant_document' => $this->whenLoaded('applicantDocument'),
        ];
    }
}