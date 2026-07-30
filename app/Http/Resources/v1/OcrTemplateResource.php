<?php

// app/Http/Resources/v1/OcrTemplateResource.php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OcrTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,

            // Template Info
            'document_type_id'         => $this->document_type_id,
            'name'                     => $this->name,
            'code'                     => $this->code,
            'version'                  => $this->version,
            'description'              => $this->description,
            'thumbnail_path'           => $this->thumbnail_path,

            // Detection
            'detection_keywords'       => $this->detection_keywords,
            'detection_patterns'       => $this->detection_patterns,
            'detection_features'       => $this->detection_features,
            'sample_image_path'        => $this->sample_image_path,
            'detection_threshold'      => $this->detection_threshold,

            // Specifications
            'expected_width'           => $this->expected_width,
            'expected_height'          => $this->expected_height,
            'aspect_ratio'             => $this->aspect_ratio,
            'orientation'              => $this->orientation,
            'paper_size'               => $this->paper_size,
            'expected_pages'           => $this->expected_pages,
            'color_mode'               => $this->color_mode,

            // Fields
            'field_definitions'        => $this->field_definitions,
            'field_positions'          => $this->field_positions,
            'field_relationships'      => $this->field_relationships,
            'validation_rules'         => $this->validation_rules,
            'required_fields'          => $this->required_fields,
            'optional_fields'          => $this->optional_fields,

            // OCR Provider
            'preferred_provider'       => $this->preferred_provider,
            'provider_settings'        => $this->provider_settings,
            'fallback_providers'       => $this->fallback_providers,
            'confidence_threshold'     => $this->confidence_threshold,

            // Image Processing
            'requires_preprocessing'   => $this->requires_preprocessing,
            'preprocessing_steps'      => $this->preprocessing_steps,
            'auto_rotate'              => $this->auto_rotate,
            'auto_enhance'             => $this->auto_enhance,
            'auto_deskew'              => $this->auto_deskew,
            'remove_background'        => $this->remove_background,
            'binarize'                 => $this->binarize,

            // Language & Region
            'primary_language'         => $this->primary_language,
            'supported_languages'      => $this->supported_languages,
            'country_code'             => $this->country_code,
            'region'                   => $this->region,

            // Performance Statistics
            'times_used'               => $this->times_used,
            'successful_scans'         => $this->successful_scans,
            'failed_scans'             => $this->failed_scans,
            'success_rate'             => $this->success_rate,
            'avg_confidence'           => $this->avg_confidence,
            'avg_processing_time_ms'   => $this->avg_processing_time_ms,
            'last_used_at'             => $this->last_used_at,

            // Learning
            'correction_count'         => $this->correction_count,
            'common_errors'            => $this->common_errors,
            'improvement_suggestions'  => $this->improvement_suggestions,
            'last_trained_at'          => $this->last_trained_at,

            // Status & Config
            'is_active'                => $this->is_active,
            'is_default'               => $this->is_default,
            'is_verified'              => $this->is_verified,
            'is_public'                => $this->is_public,
            'is_beta'                  => $this->is_beta,
            'priority'                 => $this->priority,

            // Access Control
            'allowed_roles'            => $this->allowed_roles,
            'restricted_users'         => $this->restricted_users,

            // Meta
            'notes'                    => $this->notes,
            'tags'                     => $this->tags,
            'metadata'                 => $this->metadata,
            'changelog'                => $this->changelog,

            // Approval
            'approved_at'              => $this->approved_at,

            // Timestamps
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'deleted_at'               => $this->deleted_at,

            // Relations
            'document_type'            => $this->whenLoaded('documentType'),
            'created_by_user'          => $this->whenLoaded('createdByUser'),
            'updated_by_user'          => $this->whenLoaded('updatedByUser'),
            'approved_by_user'         => $this->whenLoaded('approvedByUser'),
        ];
    }
}