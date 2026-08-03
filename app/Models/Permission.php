<?php
// app/Models/Permission.php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'module',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-derive module from name if not set
     * e.g. "role.viewAny" → "role"
     */
    public function getModuleAttribute(?string $value): ?string
    {
        if ($value) {
            return $value;
        }

        $parts = explode('.', $this->name);
        return $parts[0] ?? 'general';
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}