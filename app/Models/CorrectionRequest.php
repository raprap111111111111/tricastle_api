<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorrectionRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'request_code',
        'document_verification_id',
        'applicant_document_id',
        'requested_by',
        'severity',
        'status',
        'description',
        'fields_to_correct',
        'correction_data',
        'justification',
        'requires_approval',
        'requires_new_document',
        'due_date',
    ];

    protected $casts = [
        'fields_to_correct'     => 'array',
        'correction_data'       => 'array',
        'requires_approval'     => 'boolean',
        'requires_new_document' => 'boolean',
        'due_date'              => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function documentVerification()
    {
        return $this->belongsTo(DocumentVerification::class);
    }

    public function applicantDocument()
    {
        return $this->belongsTo(ApplicantDocument::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
    }

    public function scopeDueSoon($query, int $days = 3)
    {
        return $query->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addDays($days)])
            ->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnderReview(): bool
    {
        return $this->status === 'under_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && ! in_array($this->status, ['completed', 'cancelled', 'rejected']);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'under_review', 'approved']);
    }

    // ==========================================
    // Auto-generate request_code
    // ==========================================

    protected static function booted(): void
    {
        static::creating(function (CorrectionRequest $correctionRequest) {
            if (empty($correctionRequest->request_code)) {
                $year  = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $correctionRequest->request_code = 'CR-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}