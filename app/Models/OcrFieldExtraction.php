<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OcrFieldExtraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ocr_job_id',
        'applicant_document_id',
        'field_name',
        'field_label',
        'field_type',
        'field_category',
        'is_required',
        'is_primary_field',
        'sort_order',
        'extracted_value',
        'normalized_value',
        'validated_value',
        'final_value',
        'display_value',
        'confidence_score',
        'confidence_level',
        'character_confidence',
        'word_confidence',
        'character_count',
        'word_count',
        'bounding_box',
        'page_number',
        'x_coordinate',
        'y_coordinate',
        'width',
        'height',
        'rotation_angle',
        'passed_validation',
        'has_validation_errors',
        'validation_errors',
        'validation_rule_used',
        'validation_details',
        'status',
        'was_manually_corrected',
        'original_ocr_value',
        'corrected_by',
        'correction_reason',
        'corrected_at',
        'correction_count',
        'matches_other_documents',
        'cross_reference_matches',
        'has_conflicts',
        'conflict_details',
        'suggested_alternatives',
        'spell_check_score',
        'has_typo_suggestions',
        'notes',
        'metadata',
        'source',
    ];

    protected $casts = [
        'bounding_box'            => 'array',
        'validation_details'      => 'array',
        'cross_reference_matches' => 'array',
        'conflict_details'        => 'array',
        'suggested_alternatives'  => 'array',
        'metadata'                => 'array',
        'is_required'             => 'boolean',
        'is_primary_field'        => 'boolean',
        'passed_validation'       => 'boolean',
        'has_validation_errors'   => 'boolean',
        'was_manually_corrected'  => 'boolean',
        'matches_other_documents' => 'boolean',
        'has_conflicts'           => 'boolean',
        'has_typo_suggestions'    => 'boolean',
        'confidence_score'        => 'decimal:2',
        'character_confidence'    => 'decimal:2',
        'word_confidence'         => 'decimal:2',
        'spell_check_score'       => 'decimal:2',
        'x_coordinate'            => 'decimal:2',
        'y_coordinate'            => 'decimal:2',
        'width'                   => 'decimal:2',
        'height'                  => 'decimal:2',
        'rotation_angle'          => 'decimal:2',
        'corrected_at'            => 'datetime',
    ];

    // =========================================
    // Relationships
    // =========================================

    public function ocrJob()
    {
        return $this->belongsTo(OcrJob::class);
    }

    public function applicantDocument()
    {
        return $this->belongsTo(ApplicantDocument::class);
    }

    public function corrector()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    // =========================================
    // Scopes
    // =========================================

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRequiresReview($query)
    {
        return $query->where('status', 'requires_review');
    }

    public function scopePrimaryFields($query)
    {
        return $query->where('is_primary_field', true);
    }

    public function scopeByConfidenceLevel($query, string $level)
    {
        return $query->where('confidence_level', $level);
    }

    public function scopeManuallyCorrected($query)
    {
        return $query->where('was_manually_corrected', true);
    }

    public function scopeHasConflicts($query)
    {
        return $query->where('has_conflicts', true);
    }

    public function scopeByFieldName($query, string $fieldName)
    {
        return $query->where('field_name', $fieldName);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}