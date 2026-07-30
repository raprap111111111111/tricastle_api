<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_document_id',
        'verified_by',
        'reviewed_by',
        'status',
        'verification_data',
        'source_data',
        'match_percentage',
        'total_fields',
        'matched_fields',
        'mismatched_fields',
        'missing_fields',
        'notes',
        'rejection_reason',
        'started_at',
        'completed_at',
        'time_spent_seconds',
    ];

    protected $casts = [
        'verification_data'  => 'array',
        'source_data'        => 'array',
        'match_percentage'   => 'decimal:2',
        'total_fields'       => 'integer',
        'matched_fields'     => 'integer',
        'mismatched_fields'  => 'integer',
        'missing_fields'     => 'integer',
        'time_spent_seconds' => 'integer',
        'started_at'         => 'datetime',
        'completed_at'       => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicantDocument()
    {
        return $this->belongsTo(ApplicantDocument::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function mismatches()
    {
        return $this->hasMany(VerificationMismatch::class, 'document_verification_id');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRequiresCorrection($query)
    {
        return $query->where('status', 'requires_correction');
    }

    public function scopeByVerifier($query, int $userId)
    {
        return $query->where('verified_by', $userId);
    }

    public function scopeHighMatch($query, float $threshold = 90.0)
    {
        return $query->where('match_percentage', '>=', $threshold);
    }

    public function scopeLowMatch($query, float $threshold = 70.0)
    {
        return $query->where('match_percentage', '<', $threshold);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function calculateMatchPercentage(): float
    {
        if ($this->total_fields === 0) {
            return 0.0;
        }

        return round(($this->matched_fields / $this->total_fields) * 100, 2);
    }

    public function getTimeSpentFormatted(): string
    {
        if (! $this->time_spent_seconds) {
            return '0s';
        }

        $hours   = intdiv($this->time_spent_seconds, 3600);
        $minutes = intdiv($this->time_spent_seconds % 3600, 60);
        $seconds = $this->time_spent_seconds % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m {$seconds}s";
        }

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }
}