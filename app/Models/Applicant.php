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

class Applicant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_code',

        // ─── Personal ────────────────────────────────
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

        // ─── Physical ────────────────────────────────
        'height_cm',
        'weight_kg',
        'dominant_hand',
        'blood_type',

        // ─── Address ─────────────────────────────────
        'current_address',
        'permanent_address',
        'city',
        'province',
        'postal_code',

        // ─── Passport / IDs ──────────────────────────
        'passport_number',
        'passport_expiry',
        'sss_number',
        'tin_number',
        'philhealth_number',
        'pagibig_number',

        // ─── Status ──────────────────────────────────
        'status',
        'rejection_reason',
        'final_listed_at',
        'rejected_at',

        // ─── Quality ─────────────────────────────────
        'quality_score',
        'quality_grade',

        // ─── Staff ───────────────────────────────────
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
        'date_of_birth'      => 'date',
        'passport_expiry'    => 'date',
        'final_listed_at'    => 'datetime',
        'rejected_at'        => 'datetime',
        'number_of_children' => 'integer',
        'height_cm'          => 'decimal:2',
        'weight_kg'          => 'decimal:2',
        'quality_score'      => 'decimal:2',
        'status'             => ApplicantStatus::class,
    ];

    protected $appends = ['full_name', 'age'];

    // ═══════════════════════════════════════════════════════
    // Boot — Auto-generate applicant_code (collision-safe)
    // ═══════════════════════════════════════════════════════
    protected static function booted(): void
    {
        static::creating(function (Applicant $applicant) {
            if (empty($applicant->applicant_code)) {
                $applicant->applicant_code = static::generateUniqueCode();
            }

            // Always starts as pending
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
            get: fn () => trim(implode(' ', array_filter([
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
            get: fn () => $this->date_of_birth?->age,
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

    // ─── Batch Relationships ─────────────────────────────
    // Direct model relationship (preferred — full ApplicantBatch model)
    public function applicantBatches(): HasMany
    {
        return $this->hasMany(ApplicantBatch::class);
    }

    // Many-to-many shortcut (use only for quick batch queries)
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
}