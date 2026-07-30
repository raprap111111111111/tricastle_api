<?php

namespace App\Models;

use App\Domain\ApplicantEducation\Enums\EducationLevel;
use App\Domain\ApplicantEducation\Enums\EducationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantEducation extends Model
{
    use HasFactory;

    protected $table = 'applicant_educations';

    protected $fillable = [
        'applicant_id',
        'education_level',
        'education_status',
        'school_name',
        'course',
        'year_started',
        'year_ended',
        'honors',
    ];

    protected $casts = [
        'education_level'  => EducationLevel::class,
        'education_status' => EducationStatus::class,
        'year_started'     => 'integer',
        'year_ended'       => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}