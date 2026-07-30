<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'document_type_id',
        'file_repository_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'mime_type',
        'file_hash',
        'extracted_data',
        'validated_data',
        'ocr_confidence',
        'status',
        'document_date',
        'expiry_date',
        'is_expired',
        'expiry_notified',
        'version',
        'is_current_version',
        'last_verified_at',
        'last_verified_by',
        'uploaded_by',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'metadata',
        'notes',
        'priority',
    ];

    protected $casts = [
        'extracted_data'     => 'array',
        'validated_data'     => 'array',
        'metadata'           => 'array',
        'document_date'      => 'date',
        'expiry_date'        => 'date',
        'last_verified_at'   => 'datetime',
        'rejected_at'        => 'datetime',
        'is_expired'         => 'boolean',
        'expiry_notified'    => 'boolean',
        'is_current_version' => 'boolean',
        'file_size'          => 'integer',
        'version'            => 'integer',
        'ocr_confidence'     => 'decimal:2',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function fileRepository()
    {
        return $this->belongsTo(FileRepository::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'last_verified_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeUploaded($query)
    {
        return $query->where('status', 'uploaded');
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status', 'pending_verification');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeRequiresCorrection($query)
    {
        return $query->where('status', 'requires_correction');
    }

    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now());
    }

    public function scopeForApplicant($query, int $applicantId)
    {
        return $query->where('applicant_id', $applicantId);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['uploaded', 'pending_verification', 'under_review']);
    }

    public function checkIfExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}