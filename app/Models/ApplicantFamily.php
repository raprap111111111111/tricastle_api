<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantFamily extends Model
{
    protected $table = 'applicant_family';

    protected $fillable = [
        'applicant_id',
        'spouse_name',
        'spouse_occupation',
        'spouse_salary',
        'spouse_salary_unit',
        'father_name',
        'mother_name',
    ];

    protected $casts = [
        'spouse_salary' => 'decimal:2',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}