<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// ✅ Spatie Activitylog v5
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ApplicantDocument extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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

    // ═══════════════════════════════════════════════════════
    // 🎯 Spatie Activity Log (v5)
    // ═══════════════════════════════════════════════════════
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'applicant_id',
                'document_type_id',
                'file_name',
                'file_size',
                'status',
                'document_date',
                'expiry_date',
                'version',
                'is_current_version',
                'rejection_reason',
                'priority',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges() // ✅ was dontSubmitEmptyLogs()
            ->useLogName('Document')
            ->setDescriptionForEvent(function (string $event) {
                $fileName      = $this->file_name ?? 'unknown file';
                $applicantCode = $this->applicant?->applicant_code ?? "applicant #{$this->applicant_id}";
                $docType       = $this->documentType?->name ?? 'document';

                if ($event === 'updated' && $this->isDirty('status')) {
                    $newStatus = $this->status;
                    $readable  = str_replace('_', ' ', (string) $newStatus);

                    return match ($newStatus) {
                        'verified' => "Verified {$docType} '{$fileName}' for {$applicantCode}",
                        'rejected' => "Rejected {$docType} '{$fileName}' for {$applicantCode}",
                        'expired'  => "Marked {$docType} '{$fileName}' as expired",
                        default    => "Changed {$docType} '{$fileName}' status to {$readable}",
                    };
                }

                if ($event === 'created' && ($this->version ?? 1) > 1) {
                    return "Uploaded new version (v{$this->version}) of {$docType} for {$applicantCode}";
                }

                return match ($event) {
                    'created' => "Uploaded {$docType} '{$fileName}' for {$applicantCode}",
                    'updated' => "Updated {$docType} '{$fileName}' for {$applicantCode}",
                    'deleted' => "Deleted {$docType} '{$fileName}' from {$applicantCode}",
                    default   => "Document '{$fileName}' was {$event}",
                };
            });
    }

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function fileRepository(): BelongsTo
    {
        return $this->belongsTo(FileRepository::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_verified_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════
    public function scopeUploaded(Builder $query): Builder
    {
        return $query->where('status', 'uploaded');
    }

    public function scopePendingVerification(Builder $query): Builder
    {
        return $query->where('status', 'pending_verification');
    }

    public function scopeUnderReview(Builder $query): Builder
    {
        return $query->where('status', 'under_review');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired');
    }

    public function scopeRequiresCorrection(Builder $query): Builder
    {
        return $query->where('status', 'requires_correction');
    }

    public function scopeCurrentVersion(Builder $query): Builder
    {
        return $query->where('is_current_version', true);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now());
    }

    public function scopeForApplicant(Builder $query, int $applicantId): Builder
    {
        return $query->where('applicant_id', $applicantId);
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════
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
        return in_array($this->status, ['uploaded', 'pending_verification', 'under_review'], true);
    }

    public function checkIfExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}