<?php

namespace App\Models;

use App\Enums\BatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_number',
        'name',
        'country',
        'deployment_date',
        'status',
        'is_active',
        'description',
    ];

    protected $casts = [
        'deployment_date' => 'date',
        'is_active'       => 'boolean',
        'status'          => BatchStatus::class,
    ];

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════

    public function applicantBatches(): HasMany
    {
        return $this->hasMany(ApplicantBatch::class);
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(Applicant::class, 'applicant_batches')
                    ->withPivot([
                        'id',
                        'status',
                        'assigned_at',
                        'interview_date',
                        'medical_date',
                        'exam_date',
                        'accepted_at',
                        'deployed_at',
                        'exam_score',
                        'interview_notes',
                        'medical_notes',
                        'rejection_reason',
                        'remarks',
                        'processed_by',
                    ])
                    ->withTimestamps();
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════

    /**
     * Get the currently active batch (only one allowed).
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }
}