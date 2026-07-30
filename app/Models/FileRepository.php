<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FileRepository extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'file_repository';

    protected $fillable = [
        'file_hash',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'disk',
        'storage_driver',
        'reference_count',
        'metadata',
        'is_encrypted',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size'       => 'integer',
        'reference_count' => 'integer',
        'metadata'        => 'array',
        'is_encrypted'    => 'boolean',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ==========================================
    // Accessors
    // ==========================================

    protected function fileSizeFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
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
        );
    }

    protected function extension(): Attribute
    {
        return Attribute::make(
            get: fn () => pathinfo($this->original_name, PATHINFO_EXTENSION),
        );
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeByDisk($query, string $disk)
    {
        return $query->where('disk', $disk);
    }

    public function scopeByMimeType($query, string $mimeType)
    {
        return $query->where('mime_type', $mimeType);
    }

    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }

    public function scopeUnused($query)
    {
        return $query->where('reference_count', 0);
    }

    public function scopeByUploader($query, int $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function incrementReferenceCount(): void
    {
        $this->increment('reference_count');
    }

    public function decrementReferenceCount(): void
    {
        if ($this->reference_count > 0) {
            $this->decrement('reference_count');
        }
    }

    public function isUnused(): bool
    {
        return $this->reference_count === 0;
    }
}