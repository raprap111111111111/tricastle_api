<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OcrTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_type_id',
        'name',
        'code',
        'version',
        'description',
        'thumbnail_path',
        'detection_keywords',
        'detection_patterns',
        'detection_features',
        'sample_image_path',
        'detection_threshold',
        'expected_width',
        'expected_height',
        'aspect_ratio',
        'orientation',
        'paper_size',
        'expected_pages',
        'color_mode',
        'field_definitions',
        'field_positions',
        'field_relationships',
        'validation_rules',
        'required_fields',
        'optional_fields',
        'preferred_provider',
        'provider_settings',
        'fallback_providers',
        'confidence_threshold',
        'requires_preprocessing',
        'preprocessing_steps',
        'auto_rotate',
        'auto_enhance',
        'auto_deskew',
        'remove_background',
        'binarize',
        'primary_language',
        'supported_languages',
        'country_code',
        'region',
        'times_used',
        'successful_scans',
        'failed_scans',
        'success_rate',
        'avg_confidence',
        'avg_processing_time_ms',
        'last_used_at',
        'correction_count',
        'common_errors',
        'improvement_suggestions',
        'last_trained_at',
        'is_active',
        'is_default',
        'is_verified',
        'is_public',
        'is_beta',
        'priority',
        'allowed_roles',
        'restricted_users',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'notes',
        'tags',
        'metadata',
        'changelog',
    ];

    protected $casts = [
        'detection_keywords' => 'array',
        'detection_patterns' => 'array',
        'detection_features' => 'array',
        'field_definitions' => 'array',
        'field_positions' => 'array',
        'field_relationships' => 'array',
        'validation_rules' => 'array',
        'required_fields' => 'array',
        'optional_fields' => 'array',
        'provider_settings' => 'array',
        'fallback_providers' => 'array',
        'preprocessing_steps' => 'array',
        'supported_languages' => 'array',
        'common_errors' => 'array',
        'improvement_suggestions' => 'array',
        'allowed_roles' => 'array',
        'restricted_users' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'requires_preprocessing' => 'boolean',
        'auto_rotate' => 'boolean',
        'auto_enhance' => 'boolean',
        'auto_deskew' => 'boolean',
        'remove_background' => 'boolean',
        'binarize' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'is_beta' => 'boolean',
        'detection_threshold' => 'decimal:2',
        'aspect_ratio' => 'decimal:3',
        'success_rate' => 'decimal:2',
        'avg_confidence' => 'decimal:2',
        'avg_processing_time_ms' => 'decimal:2',
        'last_used_at' => 'datetime',
        'last_trained_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function ocrJobs()
    {
        return $this->hasMany(OcrJob::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
    public function scopeByDocumentType($query, int $docTypeId)
    {
        return $query->where('document_type_id', $docTypeId);
    }

    // Methods
    public function incrementUsage(): void
    {
        $this->increment('times_used');
        $this->update(['last_used_at' => now()]);
    }
}
