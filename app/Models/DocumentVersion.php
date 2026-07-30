<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_document_id',
        'version_number',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'extracted_data',
        'change_reason',
        'uploaded_by',
        'is_current',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'file_size'      => 'integer',
        'version_number' => 'integer',
        'is_current'     => 'boolean',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function applicantDocument()
    {
        return $this->belongsTo(ApplicantDocument::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeNotCurrent($query)
    {
        return $query->where('is_current', false);
    }

    public function scopeForDocument($query, int $applicantDocumentId)
    {
        return $query->where('applicant_document_id', $applicantDocumentId);
    }

    public function scopeByVersionNumber($query, int $version)
    {
        return $query->where('version_number', $version);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('version_number');
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isCurrent(): bool
    {
        return $this->is_current === true;
    }

    public function getFileSizeFormatted(): string
    {
        if (! $this->file_size) {
            return '0 B';
        }

        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function getExtension(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}