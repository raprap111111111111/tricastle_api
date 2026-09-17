<?php

namespace App\Models;

use App\Enums\CivilStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantGuarantor extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'sequence',
        'full_name',
        'date_of_birth',
        'age', 
        'civil_status',
        'nationality',
        'address',
        'residence_cert_no',
        'residence_cert_issued_at',
        'residence_cert_place',
        'relationship',
    ];

    protected $casts = [
        'sequence'                 => 'integer',
        'age'                      => 'integer',
        'date_of_birth'            => 'date',
        'residence_cert_issued_at' => 'date',
        'civil_status'             => CivilStatus::class,
    ];

    protected $appends = ['computed_age'];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function getComputedAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) {
            return $this->age; 
        }

        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Safely mutate civil_status to a scalar string before database insertion
     */
    public function setCivilStatusAttribute($value): void
    {
        if ($value instanceof CivilStatus) {
            $this->attributes['civil_status'] = $value->value;
        } elseif (is_string($value)) {
            $enum = CivilStatus::tryFrom(strtolower($value));
            $this->attributes['civil_status'] = $enum ? $enum->value : strtolower($value);
        } else {
            $this->attributes['civil_status'] = null;
        }
    }
}