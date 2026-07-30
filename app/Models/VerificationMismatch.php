<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationMismatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_verification_id',
        'field_name',
        'field_label',
        'source_value',
        'entered_value',
        'severity',
        'mismatch_type',
        'status',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function documentVerification()
    {
        return $this->belongsTo(DocumentVerification::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeEscalated($query)
    {
        return $query->where('status', 'escalated');
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['corrected', 'waived']);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['open', 'correction_requested', 'escalated']);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByMismatchType($query, string $type)
    {
        return $query->where('mismatch_type', $type);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isResolved(): bool
    {
        return in_array($this->status, ['corrected', 'waived']);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isEscalated(): bool
    {
        return $this->status === 'escalated';
    }
}