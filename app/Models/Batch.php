<?php

namespace App\Models;

use App\Enums\BatchStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Batch extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'batch_number',
        'name',
        'country',
        'deployment_date',
        'status',
        'is_active',
        'description',
        // fillable
        'internship_program_id',
        'receiving_company_id',
        'accepting_company_id',
        'contract_start',
        'contract_end',
        'contract_years',
        'stipend_amount',
        'meal_allowance_amount',
        'work_days',
        'day_off',
        'time_start',
        'time_end',
        'lunch_break',
        'default_job_description',
        'place_of_internship_override',
        'municipality',


    ];

    protected $casts = [
        'deployment_date' => 'date',
        'is_active'       => 'boolean',
        'status'          => BatchStatus::class,
    ];

    // ═══════════════════════════════════════════════════════
    // 🎯 Spatie Activity Log
    // ═══════════════════════════════════════════════════════
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'batch_number',
                'name',
                'country',
                'deployment_date',
                'status',
                'is_active',
                'description',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('Batch')
            ->setDescriptionForEvent(function (string $event) {
                $number = $this->batch_number ?? '?';
                $name   = $this->name ?? 'Untitled';

                // Smart descriptions
                if ($event === 'updated' && $this->isDirty('is_active')) {
                    return $this->is_active
                        ? "Activated batch #{$number} ({$name})"
                        : "Deactivated batch #{$number} ({$name})";
                }

                if ($event === 'updated' && $this->isDirty('status')) {
                    $newStatus = $this->status?->value ?? $this->status;
                    $readable  = str_replace('_', ' ', $newStatus);

                    return "Changed batch #{$number} status → {$readable}";
                }

                return match ($event) {
                    'created' => "Created batch #{$number} ({$name})",
                    'updated' => "Updated batch #{$number} ({$name})",
                    'deleted' => "Deleted batch #{$number} ({$name})",
                    default   => "Batch #{$number} was {$event}",
                };
            });
    }

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════
    public function internshipProgram(): BelongsTo
    {
        return $this->belongsTo(InternshipProgram::class);
    }

    public function receivingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'receiving_company_id');
    }

    public function acceptingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'accepting_company_id');
    }

    public function internships(): HasMany
    {
        return $this->hasMany(ApplicantInternship::class);
    }
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
    public function scopeActive(Builder $query): Builder
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
