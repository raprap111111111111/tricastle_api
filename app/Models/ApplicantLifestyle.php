<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantLifestyle extends Model
{
    use HasFactory;

    protected $table = 'applicant_lifestyle';

    protected $fillable = [
        'applicant_id',

        // Current habits
        'is_smoking',
        'is_drinking_alcohol',
        'is_using_drugs',

        // Past habits
        'was_smoking',
        'was_drinking_alcohol',
        'was_using_drugs',

        // Frequencies / notes
        'smoking_frequency',
        'drinking_frequency',
        'drugs_notes',

        // Health
        'has_medical_condition',
        'medical_notes',
        'has_allergies',
        'allergies_notes',
    ];

    protected $casts = [
        'is_smoking'            => 'boolean',
        'is_drinking_alcohol'   => 'boolean',
        'is_using_drugs'        => 'boolean',
        'was_smoking'           => 'boolean',
        'was_drinking_alcohol'  => 'boolean',
        'was_using_drugs'       => 'boolean',
        'has_medical_condition' => 'boolean',
        'has_allergies'         => 'boolean',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}