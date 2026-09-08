<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Company extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'name_japanese',
        'category_id',
        'address',
        'city',
        'prefecture',
        'postal_code',
        'country',
        'contact_person',
        'contact_email',
        'contact_phone',
        'description',
        'is_active',

        // fillable additions
        'name_on_document',
        'registration_no',
        'signatory_name',
        'signatory_title',
        'signatory_passport',
        'signatory_id_type',
        'signatory_id_no',
        'signatory_id_issued',
        'site_address',
        'industry',

        // activity logOnly additions
        'name_on_document',
        'signatory_name',
        'signatory_title',
        'is_active',


    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'country' => 'Japan',
    ];

    // ═══════════════════════════════════════════════════════
    // 🎯 Spatie Activity Log
    // ═══════════════════════════════════════════════════════
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'code',
                'name',
                'name_japanese',
                'category_id',
                'address',
                'city',
                'prefecture',
                'postal_code',
                'country',
                'contact_person',
                'contact_email',
                'contact_phone',
                'description',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('Company')
            ->setDescriptionForEvent(function (string $event) {
                $code = $this->code ?? '?';
                $name = $this->name ?? 'Untitled';

                // Smart descriptions
                if ($event === 'updated' && $this->isDirty('is_active')) {
                    return $this->is_active
                        ? "Activated company {$code} ({$name})"
                        : "Deactivated company {$code} ({$name})";
                }

                return match ($event) {
                    'created' => "Created company {$code} ({$name})",
                    'updated' => "Updated company {$code} ({$name})",
                    'deleted' => "Deleted company {$code} ({$name})",
                    default   => "Company {$code} was {$event}",
                };
            });
    }

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════
    public function dispatchedPrograms(): HasMany
    {
        return $this->hasMany(InternshipProgram::class, 'dispatching_company_id');
    }

    public function acceptedPrograms(): HasMany
    {
        return $this->hasMany(InternshipProgram::class, 'accepting_company_id');
    }

    public function receivedInternships(): HasMany
    {
        return $this->hasMany(ApplicantInternship::class, 'receiving_company_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CompanyCategory::class, 'category_id');
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
