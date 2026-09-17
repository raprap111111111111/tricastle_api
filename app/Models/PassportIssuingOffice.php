<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PassportIssuingOffice extends Model
{
    use HasFactory;

    protected $table = 'passport_issuing_offices';

    protected $fillable = [
        'region',
        'name',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'passport_issuing_office_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}