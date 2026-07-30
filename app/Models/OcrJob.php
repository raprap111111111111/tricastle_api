<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OcrJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_code',
        'batch_id',
        'external_job_id',
        'applicant_document_id',
        'file_repository_id',
        'ocr_template_id',
        'status',
        'status_message',
        'provider',
        'provider_version',
        'provider_config',
        'detected_document_type',
        'detection_confidence',
        'is_document_type_matched',
        'alternative_detections',
        'total_fields_expected',
        'total_fields_extracted',
        'total_fields_validated',
        'very_high_confidence_fields',
        'high_confidence_fields',
        'medium_confidence_fields',
        'low_confidence_fields',
        'very_low_confidence_fields',
        'missing_fields',
        'overall_confidence',
        'attempt_number',
        'max_attempts',
        'processing_time_ms',
        'queue_wait_time_ms',
        'image_size_bytes',
        'image_width',
        'image_height',
        'page_count',
        'image_format',
        'image_quality_score',
        'image_sharpness',
        'image_brightness',
        'is_rotated',
        'rotation_angle',
        'is_blurry',
        'has_glare',
        'is_upside_down',
        'raw_response',
        'extracted_fields',
        'bounding_boxes',
        'extracted_text',
        'detected_languages',
        'error_message',
        'error_code',
        'error_details',
        'is_recoverable_error',
        'api_cost',
        'cost_currency',
        'cost_per_page',
        'is_free_tier',
        'initiated_by',
        'reviewed_by',
        'cancelled_by',
        'queued_at',
        'started_at',
        'completed_at',
        'reviewed_at',
        'cancelled_at',
        'failed_at',
        'retry_at',
        'notes',
        'metadata',
        'priority',
    ];

    protected $casts = [
        'provider_config' => 'array',
        'alternative_detections' => 'array',
        'extracted_fields' => 'array',
        'bounding_boxes' => 'array',
        'detected_languages' => 'array',
        'error_details' => 'array',
        'metadata' => 'array',
        'is_document_type_matched' => 'boolean',
        'is_rotated' => 'boolean',
        'is_blurry' => 'boolean',
        'has_glare' => 'boolean',
        'is_upside_down' => 'boolean',
        'is_recoverable_error' => 'boolean',
        'is_free_tier' => 'boolean',
        'detection_confidence' => 'decimal:2',
        'overall_confidence' => 'decimal:2',
        'image_quality_score' => 'decimal:2',
        'image_sharpness' => 'decimal:2',
        'image_brightness' => 'decimal:2',
        'api_cost' => 'decimal:6',
        'cost_per_page' => 'decimal:6',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'failed_at' => 'datetime',
        'retry_at' => 'datetime',
    ];

    // Relationships
    public function document()
    {
        return $this->belongsTo(ApplicantDocument::class, 'applicant_document_id');
    }
    public function fileRepository()
    {
        return $this->belongsTo(FileRepository::class);
    }
    public function template()
    {
        return $this->belongsTo(OcrTemplate::class, 'ocr_template_id');
    }
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function fieldExtractions()
    {
        return $this->hasMany(OcrFieldExtraction::class);
    }
    public function corrections()
    {
        return $this->hasMany(OcrManualCorrection::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    public function scopeRequiresReview($query)
    {
        return $query->where('status', 'requires_review');
    }
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    // Auto-generate job code
    protected static function booted()
    {
        static::creating(function ($job) {
            if (empty($job->job_code)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $job->job_code = 'OCR-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
