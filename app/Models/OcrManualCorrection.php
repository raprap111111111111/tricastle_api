<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcrManualCorrection extends Model
{
    protected $fillable = [
        // Relationships
        'ocr_job_id',
        'ocr_field_extraction_id',
        'applicant_document_id',
        'ocr_template_id',

        // Field Information
        'field_name',
        'field_label',
        'field_type',

        // Correction Details
        'original_value',
        'corrected_value',
        'previous_correction',
        'reason',
        'explanation',

        // Classification
        'correction_type',
        'severity',

        // Impact Metrics
        'confidence_before',
        'confidence_after',
        'characters_changed',
        'similarity_score',
        'edit_distance',
        'was_critical_field',

        // User Info
        'corrected_by',
        'verified_by',
        'reviewed_by',

        // Verification
        'is_verified',
        'verified_at',
        'verification_notes',
        'is_disputed',
        'dispute_reason',

        // ML / Training
        'used_for_training',
        'trained_at',
        'training_batch_id',
        'improved_accuracy',
        'accuracy_improvement',
        'added_to_pattern_library',

        // Pattern Detection
        'is_recurring_error',
        'occurrence_count',
        'similar_correction_ids',
        'error_pattern_id',

        // Context
        'provider_used',
        'template_used',
        'image_metadata',
        'surrounding_text',
        'field_position',

        // Timing
        'time_to_correct_seconds',
        'correction_started_at',
        'correction_completed_at',

        // Additional
        'notes',
        'metadata',
        'tags',
    ];

    protected $casts = [
        'was_critical_field'        => 'boolean',
        'is_verified'               => 'boolean',
        'is_disputed'               => 'boolean',
        'used_for_training'         => 'boolean',
        'improved_accuracy'         => 'boolean',
        'added_to_pattern_library'  => 'boolean',
        'is_recurring_error'        => 'boolean',
        'confidence_before'         => 'decimal:2',
        'confidence_after'          => 'decimal:2',
        'similarity_score'          => 'decimal:2',
        'accuracy_improvement'      => 'decimal:2',
        'characters_changed'        => 'integer',
        'edit_distance'             => 'integer',
        'occurrence_count'          => 'integer',
        'time_to_correct_seconds'   => 'integer',
        'similar_correction_ids'    => 'array',
        'image_metadata'            => 'array',
        'surrounding_text'          => 'array',
        'field_position'            => 'array',
        'metadata'                  => 'array',
        'tags'                      => 'array',
        'verified_at'               => 'datetime',
        'trained_at'                => 'datetime',
        'correction_started_at'     => 'datetime',
        'correction_completed_at'   => 'datetime',
    ];

    // ============================================================
    // Correction Types
    // ============================================================
    const CORRECTION_TYPES = [
        'ocr_misread',
        'wrong_field',
        'missed_field',
        'format_error',
        'partial_extraction',
        'wrong_language',
        'poor_image_quality',
        'template_mismatch',
        'punctuation_error',
        'case_error',
        'spacing_error',
        'special_character',
        'number_letter_confusion',
        'similar_character',
        'handwritten_text',
        'stamp_or_seal',
        'other',
    ];

    // ============================================================
    // Severities
    // ============================================================
    const SEVERITIES = [
        'trivial',
        'minor',
        'moderate',
        'major',
        'critical',
    ];

    // ============================================================
    // Relationships
    // ============================================================
    public function ocrJob(): BelongsTo
    {
        return $this->belongsTo(OcrJob::class);
    }

    public function ocrFieldExtraction(): BelongsTo
    {
        return $this->belongsTo(OcrFieldExtraction::class);
    }

    public function applicantDocument(): BelongsTo
    {
        return $this->belongsTo(ApplicantDocument::class);
    }

    public function ocrTemplate(): BelongsTo
    {
        return $this->belongsTo(OcrTemplate::class);
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ============================================================
    // Scopes
    // ============================================================
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeDisputed($query)
    {
        return $query->where('is_disputed', true);
    }

    public function scopeUsedForTraining($query)
    {
        return $query->where('used_for_training', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring_error', true);
    }

    public function scopeCriticalFields($query)
    {
        return $query->where('was_critical_field', true);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByCorrectionType($query, string $type)
    {
        return $query->where('correction_type', $type);
    }

    public function scopeByFieldName($query, string $fieldName)
    {
        return $query->where('field_name', $fieldName);
    }

    public function scopeByCorrectedBy($query, int $userId)
    {
        return $query->where('corrected_by', $userId);
    }
}