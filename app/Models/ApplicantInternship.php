<?php

namespace App\Models;

use App\Enums\InternshipProgramType;
use App\Enums\InternshipStatus;
use App\Models\InternshipDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ApplicantInternship extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'applicant_id',
        'batch_id',
        'internship_program_id',
        'program_type',
        'status',
        'is_current',
        'dispatching_company_id',
        'accepting_company_id',
        'receiving_company_id',
        'previous_internship_id',
        'job_description',
        'place_of_internship',
        'municipality',
        'agreement_date',
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
        'change_reason',
        'changed_at',
        'created_by',
    ];

    protected $casts = [
        'program_type'          => InternshipProgramType::class,
        'status'                => InternshipStatus::class,
        'is_current'            => 'boolean',
        'contract_years'        => 'integer',
        'stipend_amount'        => 'integer',
        'meal_allowance_amount' => 'integer',
        'agreement_date'        => 'date',
        'contract_start'        => 'date',
        'contract_end'          => 'date',
        'changed_at'            => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'status', 'is_current', 'program_type',
                'receiving_company_id', 'accepting_company_id',
                'contract_start', 'contract_end', 'job_description',
                'stipend_amount', 'change_reason',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('ApplicantInternship')
            ->setDescriptionForEvent(function (string $event) {
                $code = $this->applicant?->applicant_code ?? "#{$this->applicant_id}";

                if ($event === 'updated' && $this->isDirty('status')) {
                    return "Internship for {$code} → {$this->status?->value}";
                }

                return match ($event) {
                    'created' => "Created internship for {$code}",
                    'updated' => "Updated internship for {$code}",
                    'deleted' => "Deleted internship for {$code}",
                    default   => "Internship for {$code} was {$event}",
                };
            });
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(InternshipProgram::class, 'internship_program_id');
    }

    public function dispatchingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'dispatching_company_id');
    }

    public function acceptingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'accepting_company_id');
    }

    public function receivingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'receiving_company_id');
    }

    public function previousInternship(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_internship_id');
    }

    public function nextInternships(): HasMany
    {
        return $this->hasMany(self::class, 'previous_internship_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InternshipDocument::class, 'applicant_internship_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', InternshipStatus::Active);
    }

    public function scopeForApplicant(Builder $query, int $applicantId): Builder
    {
        return $query->where('applicant_id', $applicantId);
    }

    public function canChangeCompany(): bool
    {
        $typeAllows = $this->program_type?->allowsCompanyTransfer() ?? false;
        $programAllows = (bool) ($this->program?->allows_company_transfer ?? false);

        return ($typeAllows || $programAllows)
            && in_array($this->status, [InternshipStatus::Active, InternshipStatus::Pending], true)
            && $this->is_current;
    }
}