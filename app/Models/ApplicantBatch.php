<?php

namespace App\Models;

use App\Enums\ApplicantBatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'applicant_batches';

    protected $fillable = [
        'applicant_id',
        'batch_id',
        'status',
        'applied_at',
        'interview_date',
        'medical_date',
        'exam_date',
        'accepted_at',
        'deployed_at',
        'exam_score',
        'interview_notes',
        'medical_notes',
        'rejection_reason',
        'processed_by',
    ];

    protected $casts = [
        'status'         => ApplicantBatchStatus::class,
        'applied_at'     => 'date',
        'interview_date' => 'date',
        'medical_date'   => 'date',
        'exam_date'      => 'date',
        'accepted_at'    => 'date',
        'deployed_at'    => 'date',
        'exam_score'     => 'decimal:2',
    ];

    // ==========================================
    // Relationships
    // ==========================================

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

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            ApplicantBatchStatus::REJECTED->value,
            ApplicantBatchStatus::WITHDRAWN->value,
        ]);
    }
}