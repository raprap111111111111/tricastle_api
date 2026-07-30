<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantEmployment extends Model
{
    use HasFactory;

    protected $table = 'applicant_employments';

    protected $fillable = [
        'applicant_id',
        'company_name',
        'position',
        'industry',
        'job_description',
        'date_started',
        'date_ended',
        'is_current',
        'country',
        'city',
        'salary',
        'salary_currency',
        'reason_for_leaving',
    ];

    protected $casts = [
        'date_started' => 'date',
        'date_ended'   => 'date',
        'is_current'   => 'boolean',
        'salary'       => 'decimal:2',
    ];

    protected $attributes = [
        'country'         => 'Philippines',
        'salary_currency' => 'PHP',
        'is_current'      => false,
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Total duration in months (based on date_started → date_ended or now).
     */
    protected function durationMonths(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->date_started) {
                return null;
            }

            $end = $this->is_current ? now() : ($this->date_ended ?? now());
            return (int) $this->date_started->diffInMonths($end);
        });
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}