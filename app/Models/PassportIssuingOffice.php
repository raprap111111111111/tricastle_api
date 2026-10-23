<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PassportIssuingOffice extends Model
{
    use HasFactory;

    protected $table = 'passport_issuing_offices';

    protected $fillable = [
        'code',
        'region',
        'name',
        'office_name',
        'address',
        'city',
        'province',
        'sort_order',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'passport_issuing_office_id');
    }

    /**
     * Scope to active offices only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order offices cleanly by region and name.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('region', 'asc')
                     ->orderBy('name', 'asc');
    }
}