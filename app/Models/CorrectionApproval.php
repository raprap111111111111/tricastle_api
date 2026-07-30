<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CorrectionApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_request_id',
        'approver_id',
        'decision',
        'comments',
        'conditions',
        'approval_level',
        'decided_at',
    ];

    protected $casts = [
        'conditions'  => 'array',
        'decided_at'  => 'datetime',
        'approval_level' => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function correctionRequest()
    {
        return $this->belongsTo(CorrectionRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('decision', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('decision', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('decision', 'rejected');
    }

    public function scopeEscalated($query)
    {
        return $query->where('decision', 'escalated');
    }

    public function scopeByLevel($query, int $level)
    {
        return $query->where('approval_level', $level);
    }

    public function scopeByApprover($query, int $approverId)
    {
        return $query->where('approver_id', $approverId);
    }

    public function scopeSupervisorLevel($query)
    {
        return $query->where('approval_level', 1);
    }

    public function scopeAdminLevel($query)
    {
        return $query->where('approval_level', 2);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isPending(): bool
    {
        return $this->decision === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->decision === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->decision === 'rejected';
    }

    public function isEscalated(): bool
    {
        return $this->decision === 'escalated';
    }

    public function isDecided(): bool
    {
        return $this->decision !== 'pending';
    }

    public function isSupervisorLevel(): bool
    {
        return $this->approval_level === 1;
    }

    public function isAdminLevel(): bool
    {
        return $this->approval_level === 2;
    }

    public function getLevelLabel(): string
    {
        return match ($this->approval_level) {
            1       => 'Supervisor',
            2       => 'Admin',
            default => "Level {$this->approval_level}",
        };
    }
}