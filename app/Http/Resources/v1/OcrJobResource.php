<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OcrJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Identification
            'id'               => $this->id,
            'job_code'         => $this->job_code,
            'batch_id'         => $this->batch_id,
            'external_job_id'  => $this->external_job_id,

            // Relationships
            'applicant_document_id' => $this->applicant_document_id,
            'file_repository_id'    => $this->file_repository_id,
            'ocr_template_id'       => $this->ocr_template_id,

            // Status
            'status'         => $this->status,
            'status_message' => $this->status_message,

            // Provider
            'provider'         => $this->provider,
            'provider_version' => $this->provider_version,
            'provider_config'  => $this->provider_config,

            // Detection
            'detected_document_type'  => $this->detected_document_type,
            'detection_confidence'    => $this->detection_confidence,
            'is_document_type_matched' => $this->is_document_type_matched,
            'alternative_detections'  => $this->alternative_detections,

            // Extraction Stats
            'extraction_stats' => [
                'total_fields_expected'       => $this->total_fields_expected,
                'total_fields_extracted'      => $this->total_fields_extracted,
                'total_fields_validated'      => $this->total_fields_validated,
                'very_high_confidence_fields' => $this->very_high_confidence_fields,
                'high_confidence_fields'      => $this->high_confidence_fields,
                'medium_confidence_fields'    => $this->medium_confidence_fields,
                'low_confidence_fields'       => $this->low_confidence_fields,
                'very_low_confidence_fields'  => $this->very_low_confidence_fields,
                'missing_fields'              => $this->missing_fields,
                'overall_confidence'          => $this->overall_confidence,
            ],

            // Processing
            'processing' => [
                'attempt_number'      => $this->attempt_number,
                'max_attempts'        => $this->max_attempts,
                'processing_time_ms'  => $this->processing_time_ms,
                'queue_wait_time_ms'  => $this->queue_wait_time_ms,
                'page_count'          => $this->page_count,
                'image_format'        => $this->image_format,
                'image_size_bytes'    => $this->image_size_bytes,
                'image_width'         => $this->image_width,
                'image_height'        => $this->image_height,
            ],

            // Image Quality
            'image_quality' => [
                'score'      => $this->image_quality_score,
                'sharpness'  => $this->image_sharpness,
                'brightness' => $this->image_brightness,
                'is_rotated' => $this->is_rotated,
                'rotation_angle' => $this->rotation_angle,
                'is_blurry'  => $this->is_blurry,
                'has_glare'  => $this->has_glare,
                'is_upside_down' => $this->is_upside_down,
            ],

            // Results
            'extracted_fields'  => $this->extracted_fields,
            'detected_languages' => $this->detected_languages,
            'extracted_text'    => $this->when(
                $request->boolean('with_text'),
                $this->extracted_text
            ),

            // Error Info
            'error' => $this->when($this->error_message, [
                'message'        => $this->error_message,
                'code'           => $this->error_code,
                'details'        => $this->error_details,
                'is_recoverable' => $this->is_recoverable_error,
            ]),

            // Cost
            'cost' => [
                'api_cost'      => $this->api_cost,
                'currency'      => $this->cost_currency,
                'cost_per_page' => $this->cost_per_page,
                'is_free_tier'  => $this->is_free_tier,
            ],

            // Users
            'initiated_by' => $this->when(
                $this->relationLoaded('initiator'),
                fn() => $this->initiator?->only('id', 'name', 'email')
            ),
            'reviewed_by' => $this->when(
                $this->relationLoaded('reviewer'),
                fn() => $this->reviewer?->only('id', 'name', 'email')
            ),

            // Priority
            'priority' => $this->priority,
            'notes'    => $this->notes,
            'metadata' => $this->metadata,

            // Timestamps
            'timestamps' => [
                'queued_at'    => $this->queued_at?->toISOString(),
                'started_at'   => $this->started_at?->toISOString(),
                'completed_at' => $this->completed_at?->toISOString(),
                'reviewed_at'  => $this->reviewed_at?->toISOString(),
                'cancelled_at' => $this->cancelled_at?->toISOString(),
                'failed_at'    => $this->failed_at?->toISOString(),
                'retry_at'     => $this->retry_at?->toISOString(),
                'created_at'   => $this->created_at?->toISOString(),
                'updated_at'   => $this->updated_at?->toISOString(),
            ],

            // Relations
            'document'       => $this->whenLoaded('document'),
            'file_repository' => $this->whenLoaded('fileRepository'),
            'template'       => $this->whenLoaded('template'),
        ];
    }
}