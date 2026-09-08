<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class InternshipDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_internship_id',
        'document_type',
        'document_no',
        'file_path',
        'status',
        'generated_by',
        'snapshot',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    protected $appends = ['download_url'];

    protected function downloadUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->file_path) {
                    return null;
                }

                $disk = $this->disk ?? (
                    config('filesystems.default') === 'local'
                    ? 'public'
                    : config('filesystems.default', 'public')
                );

                return Storage::disk($disk)->url($this->file_path);
            }
        );
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(ApplicantInternship::class, 'applicant_internship_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
