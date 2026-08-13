<?php

namespace App\Models;

use App\Enums\ApplicantStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Applicant extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        // ── Identity ──────────────────────────────────────────────────────
        'applicant_code',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'phone',
        'mobile',
        'date_of_birth',
        'gender',
        'civil_status',
        'number_of_children',
        'nationality',

        // ── Physical ──────────────────────────────────────────────────────
        'height_cm',
        'weight_kg',
        'dominant_hand',
        'blood_type',

        // ── Address ───────────────────────────────────────────────────────
        'current_address',
        'permanent_address',
        'city',
        'province',
        'postal_code',

        // ── Passport / IDs ────────────────────────────────────────────────
        'passport_number',
        'passport_expiry',
        'sss_number',
        'tin_number',
        'philhealth_number',
        'pagibig_number',

        // ── Skill / Trade (Phase 1) ───────────────────────────────────────
        'skill_category',           // skilled | semi_skilled | unskilled
        'trade_or_occupation',      // e.g. "Formwork Carpenter", "Steel Fixer"

        // ── Language (Phase 1) ────────────────────────────────────────────
        'understands_basic_english',
        'jlpt_level',               // N5 | N4 | N3 | N2 | N1

        // ── Japan Deployment Readiness (Phase 1) ──────────────────────────
        'willing_to_be_deployed',
        'japan_deployment_ready',   // staff-set after docs/medical cleared
        'preferred_work_location',

        // ── Prior Japan Experience (Phase 1) ──────────────────────────────
        'previous_japan_experience',
        'years_japan_experience',

        // ── TITP / SSW Certifications (Phase 1) ──────────────────────────
        'has_titp_certificate',
        'titp_occupation',
        'ssw_eligible',

        // ── Salary (Phase 1) ──────────────────────────────────────────────
        'expected_salary',
        'expected_salary_currency',
        'current_salary',
        'current_salary_currency',

        // ── Family (Phase 1) ──────────────────────────────────────────────
        'father_name',
        'father_occupation',
        'father_contact',
        'mother_name',
        'mother_occupation',
        'mother_contact',
        'spouse_name',
        'spouse_occupation',
        'spouse_contact',

        // ── Emergency Contact (Phase 1) ───────────────────────────────────
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_address',

        // ── Application Status ────────────────────────────────────────────
        'status',
        'rejection_reason',
        'final_listed_at',
        'rejected_at',

        // ── Quality ───────────────────────────────────────────────────────
        'quality_score',
        'quality_grade',

        // ── Staff ─────────────────────────────────────────────────────────
        'assigned_staff_id',
        'reviewed_by',
        'created_by',
    ];

    protected $with = [
        'assignedStaff',
        'creator',
        'lifestyle',
        'educations',
        'employments',
        'tattoos',
    ];

    protected $casts = [
        // ── Dates ─────────────────────────────────────────────────────────
        'date_of_birth'   => 'date',
        'passport_expiry' => 'date',
        'final_listed_at' => 'datetime',
        'rejected_at'     => 'datetime',

        // ── Numerics ──────────────────────────────────────────────────────
        'number_of_children'    => 'integer',
        'height_cm'             => 'decimal:2',
        'weight_kg'             => 'decimal:2',
        'quality_score'         => 'decimal:2',
        'expected_salary'       => 'decimal:2',
        'current_salary'        => 'decimal:2',
        'years_japan_experience'=> 'integer',

        // ── Booleans (Phase 1) ────────────────────────────────────────────
        'understands_basic_english'  => 'boolean',
        'willing_to_be_deployed'     => 'boolean',
        'japan_deployment_ready'     => 'boolean',
        'previous_japan_experience'  => 'boolean',
        'has_titp_certificate'       => 'boolean',
        'ssw_eligible'               => 'boolean',

        // ── Enum ──────────────────────────────────────────────────────────
        'status' => ApplicantStatus::class,
    ];

    protected $appends = ['full_name', 'age'];

    // ═══════════════════════════════════════════════════════
    // Spatie Activity Log
    // ═══════════════════════════════════════════════════════

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                // Personal
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'email',
                'phone',
                'mobile',

                // Status
                'status',
                'rejection_reason',
                'final_listed_at',
                'rejected_at',

                // Quality
                'quality_score',
                'quality_grade',

                // Staff
                'assigned_staff_id',

                // Phase 1 — log deployment-relevant changes
                'skill_category',
                'understands_basic_english',
                'jlpt_level',
                'willing_to_be_deployed',
                'japan_deployment_ready',
                'previous_japan_experience',
                'has_titp_certificate',
                'ssw_eligible',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('Applicant')
            ->setDescriptionForEvent(function (string $event) {
                $code = $this->applicant_code ?? 'unknown';
                $name = trim("{$this->first_name} {$this->last_name}");

                if ($event === 'updated' && $this->isDirty('status')) {
                    $newStatus = $this->status?->value ?? $this->status;

                    return match ($newStatus) {
                        'final_list' => "Moved {$code} ({$name}) to Final List",
                        'rejected'   => "Rejected {$code} ({$name})",
                        'verified'   => "Verified {$code} ({$name})",
                        default      => "Changed {$code} status to {$newStatus}",
                    };
                }

                if ($event === 'updated' && $this->isDirty('japan_deployment_ready')) {
                    $flag = $this->japan_deployment_ready ? 'Ready' : 'Not Ready';
                    return "Marked {$code} ({$name}) as Japan Deployment: {$flag}";
                }

                return match ($event) {
                    'created' => "Created applicant {$code} ({$name})",
                    'updated' => "Updated applicant {$code}",
                    'deleted' => "Deleted applicant {$code}",
                    default   => "Applicant {$code} was {$event}",
                };
            });
    }

    // ═══════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════

    protected static function booted(): void
    {
        static::creating(function (Applicant $applicant) {
            if (empty($applicant->applicant_code)) {
                $applicant->applicant_code = static::generateUniqueCode();
            }

            $applicant->status ??= ApplicantStatus::Pending;
        });
    }

    public static function generateUniqueCode(): string
    {
        $year   = now()->format('Y');
        $prefix = "TC-{$year}-";

        $lastCode = static::withTrashed()
            ->where('applicant_code', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(applicant_code, 9) AS UNSIGNED) DESC')
            ->value('applicant_code');

        $nextNumber = 1;
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 8);
            $nextNumber = $lastNumber + 1;
        }

        $code = sprintf('%s%05d', $prefix, $nextNumber);

        while (static::withTrashed()->where('applicant_code', $code)->exists()) {
            $nextNumber++;
            $code = sprintf('%s%05d', $prefix, $nextNumber);
        }

        return $code;
    }

    // ═══════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
                $this->suffix,
            ])))
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_of_birth?->age,
        );
    }

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lifestyle(): HasOne
    {
        return $this->hasOne(ApplicantLifestyle::class);
    }

    public function tattoos(): HasMany
    {
        return $this->hasMany(ApplicantTattoo::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(ApplicantEducation::class);
    }

    public function employments(): HasMany
    {
        return $this->hasMany(ApplicantEmployment::class);
    }

    public function currentEmployment(): HasOne
    {
        return $this->hasOne(ApplicantEmployment::class)
            ->where('is_current', true);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }

    public function currentDocuments(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class)
            ->where('is_current_version', true);
    }

    public function applicantBatches(): HasMany
    {
        return $this->hasMany(ApplicantBatch::class);
    }

    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'applicant_batches')
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
            ->withTimestamps()
            ->using(ApplicantBatch::class);
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════

    public function scopePending($query)
    {
        return $query->where('status', ApplicantStatus::Pending);
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', ApplicantStatus::UnderReview);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', ApplicantStatus::Verified);
    }

    public function scopeFinalList($query)
    {
        return $query->where('status', ApplicantStatus::FinalList);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', ApplicantStatus::Rejected);
    }

    public function scopeByStaff($query, int $staffId)
    {
        return $query->where('assigned_staff_id', $staffId);
    }

    // ── Phase 1 scopes ────────────────────────────────────────────────────

    public function scopeWillingToBeDeployed($query)
    {
        return $query->where('willing_to_be_deployed', true);
    }

    public function scopeJapanReady($query)
    {
        return $query->where('japan_deployment_ready', true);
    }

    public function scopeBySkill($query, string $skillCategory)
    {
        return $query->where('skill_category', $skillCategory);
    }

    public function scopeByJlptLevel($query, string $level)
    {
        return $query->where('jlpt_level', $level);
    }

    public function scopeSswEligible($query)
    {
        return $query->where('ssw_eligible', true);
    }

    public function scopeWithJapanExperience($query)
    {
        return $query->where('previous_japan_experience', true);
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════

    public function isFinalList(): bool
    {
        return $this->status === ApplicantStatus::FinalList;
    }

    public function isRejected(): bool
    {
        return $this->status === ApplicantStatus::Rejected;
    }

    public function canBeAssignedToBatch(): bool
    {
        return $this->status === ApplicantStatus::FinalList;
    }

    public function isJapanDeploymentReady(): bool
    {
        return $this->japan_deployment_ready === true;
    }

    public function hasJapanExperience(): bool
    {
        return $this->previous_japan_experience === true;
    }
}