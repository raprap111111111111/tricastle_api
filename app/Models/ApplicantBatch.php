<?php

namespace App\Models;

use App\Enums\ApplicantBatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantBatch extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'applicant_batches';

    // Pivot needs this to use as a standalone model
    public $incrementing = true;

    protected $fillable = [
        'applicant_id',
        'batch_id',

        // ─── Status ──────────────────────────────────
        'status',

        // ─── Dates ───────────────────────────────────
        'assigned_at',
        'interview_date',
        'medical_date',
        'exam_date',
        'accepted_at',
        'deployed_at',

        // ─── Scores & Notes ──────────────────────────
        'exam_score',
        'interview_notes',
        'medical_notes',
        'rejection_reason',
        'remarks',

        // ─── Processed By ────────────────────────────
        'processed_by',
    ];

    protected $casts = [
        'status'         => ApplicantBatchStatus::class,
        'assigned_at'    => 'datetime',
        'interview_date' => 'date',
        'medical_date'   => 'date',
        'exam_date'      => 'date',
        'accepted_at'    => 'datetime',
        'deployed_at'    => 'datetime',
        'exam_score'     => 'decimal:2',
    ];

    // ═══════════════════════════════════════════════════════
    // Boot — Defaults
    // ═══════════════════════════════════════════════════════
    protected static function booted(): void
    {
        static::creating(function (ApplicantBatch $applicantBatch) {
            // Always starts as assigned
            $applicantBatch->status    ??= ApplicantBatchStatus::ASSIGNED;
            $applicantBatch->assigned_at ??= now();
        });
    }

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            ApplicantBatchStatus::REJECTED->value,
            ApplicantBatchStatus::WITHDRAWN->value,
        ]);
    }

    public function scopeDeployed($query)
    {
        return $query->where('status', ApplicantBatchStatus::DEPLOYED->value);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', ApplicantBatchStatus::ACCEPTED->value);
    }

    public function scopeForBatch($query, int $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeForApplicant($query, int $applicantId)
    {
        return $query->where('applicant_id', $applicantId);
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════
    public function isDeployed(): bool
    {
        return $this->status === ApplicantBatchStatus::DEPLOYED;
    }

    public function isAccepted(): bool
    {
        return $this->status === ApplicantBatchStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === ApplicantBatchStatus::REJECTED;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === ApplicantBatchStatus::WITHDRAWN;
    }

    public function canProgress(): bool
    {
        return ! in_array($this->status, [
            ApplicantBatchStatus::REJECTED,
            ApplicantBatchStatus::WITHDRAWN,
            ApplicantBatchStatus::DEPLOYED,
        ]);
    }
}