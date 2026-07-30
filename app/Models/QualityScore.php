<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QualityScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'overall_score',
        'grade',
        'completeness_score',
        'accuracy_score',
        'consistency_score',
        'timeliness_score',
        'total_documents',
        'verified_documents',
        'rejected_documents',
        'pending_documents',
        'total_mismatches',
        'critical_mismatches',
        'open_corrections',
        'breakdown',
        'calculated_at',
        'calculated_by',
    ];

    protected $casts = [
        'overall_score'       => 'decimal:2',
        'completeness_score'  => 'decimal:2',
        'accuracy_score'      => 'decimal:2',
        'consistency_score'   => 'decimal:2',
        'timeliness_score'    => 'decimal:2',
        'total_documents'     => 'integer',
        'verified_documents'  => 'integer',
        'rejected_documents'  => 'integer',
        'pending_documents'   => 'integer',
        'total_mismatches'    => 'integer',
        'critical_mismatches' => 'integer',
        'open_corrections'    => 'integer',
        'breakdown'           => 'array',
        'calculated_at'       => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function calculator()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeByGrade($query, string $grade)
    {
        return $query->where('grade', $grade);
    }

    public function scopeHighScore($query, float $threshold = 80.0)
    {
        return $query->where('overall_score', '>=', $threshold);
    }

    public function scopeLowScore($query, float $threshold = 50.0)
    {
        return $query->where('overall_score', '<', $threshold);
    }

    public function scopeForApplicant($query, int $applicantId)
    {
        return $query->where('applicant_id', $applicantId);
    }

    public function scopeWithCriticalMismatches($query)
    {
        return $query->where('critical_mismatches', '>', 0);
    }

    // ==========================================
    // Methods
    // ==========================================

    public static function calculateGrade(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default      => 'F',
        };
    }

    public function getVerificationRate(): float
    {
        if ($this->total_documents === 0) {
            return 0.0;
        }

        return round(
            ($this->verified_documents / $this->total_documents) * 100,
            2
        );
    }

    public function getRejectionRate(): float
    {
        if ($this->total_documents === 0) {
            return 0.0;
        }

        return round(
            ($this->rejected_documents / $this->total_documents) * 100,
            2
        );
    }

    public function isPassingGrade(): bool
    {
        return in_array($this->grade, ['A', 'B', 'C']);
    }

    public function isFailingGrade(): bool
    {
        return $this->grade === 'F';
    }

    public function hasCriticalIssues(): bool
    {
        return $this->critical_mismatches > 0;
    }
}