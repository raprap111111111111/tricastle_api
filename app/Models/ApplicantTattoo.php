<?php

namespace App\Models;

use App\Enums\TattooSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ApplicantTattoo extends Model
{
    use HasFactory;

    protected $table = 'applicant_tattoos';

    protected $fillable = [
        'applicant_id',
        'location',
        'size',
        'description',
        'photo_path',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'size'       => TattooSize::class,
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

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path
            ? Storage::disk('public')->url($this->photo_path)
            : null;
    }
}