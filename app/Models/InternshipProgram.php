<?php

namespace App\Models;

use App\Enums\InternshipProgramType;
use App\Models\ApplicantInternship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class InternshipProgram extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'program_type',
        'document_template',
        'description',
        'allows_company_transfer',
        'dispatching_company_id',
        'accepting_company_id',
        'default_receiving_company_id',
        'contract_years',
        'default_municipality',
        'default_job_description',
        'training_type',
        'compensation_type',
        'stipend_amount',
        'meal_allowance_amount',
        'compensation_currency',
        'has_bonus',
        'work_days',
        'day_off',
        'time_start',
        'time_end',
        'lunch_break',
        'overtime_policy',
        'witness_user_id',
        'witness_name',
        'witness_title',
        'witness_org',
        'is_active',
    ];

    protected $casts = [
        'program_type'            => InternshipProgramType::class,
        'allows_company_transfer' => 'boolean',
        'has_bonus'               => 'boolean',
        'is_active'               => 'boolean',
        'contract_years'          => 'integer',
        'stipend_amount'          => 'integer',
        'meal_allowance_amount'   => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'code', 'name', 'program_type', 'is_active',
                'allows_company_transfer', 'stipend_amount', 'meal_allowance_amount',
                'dispatching_company_id', 'accepting_company_id', 'default_receiving_company_id',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('InternshipProgram')
            ->setDescriptionForEvent(fn (string $e) => match ($e) {
                'created' => "Created internship program {$this->code}",
                'updated' => "Updated internship program {$this->code}",
                'deleted' => "Deleted internship program {$this->code}",
                default   => "Internship program {$this->code} was {$e}",
            });
    }

    public function dispatchingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'dispatching_company_id');
    }

    public function acceptingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'accepting_company_id');
    }

    public function defaultReceivingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_receiving_company_id');
    }

    public function witnessUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'witness_user_id');
    }

    public function internships(): HasMany
    {
        return $this->hasMany(ApplicantInternship::class, 'internship_program_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'internship_program_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}