<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CompanyCategory extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ═══════════════════════════════════════════════════════
    // 🎯 Spatie Activity Log
    // ═══════════════════════════════════════════════════════
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'description',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('CompanyCategory')
            ->setDescriptionForEvent(function (string $event) {
                $name = $this->name ?? 'Untitled';

                if ($event === 'updated' && $this->isDirty('is_active')) {
                    return $this->is_active
                        ? "Activated category '{$name}'"
                        : "Deactivated category '{$name}'";
                }

                return match ($event) {
                    'created' => "Created company category '{$name}'",
                    'updated' => "Updated company category '{$name}'",
                    'deleted' => "Deleted company category '{$name}'",
                    default   => "Company category '{$name}' was {$event}",
                };
            });
    }

    // ═══════════════════════════════════════════════════════
    // Boot — Auto-slug
    // ═══════════════════════════════════════════════════════
    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (self $category) {
            if ($category->isDirty('name') && ! $category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}