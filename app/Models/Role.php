<?php
// app/Models/Role.php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    /**
     * Mass assignable — extends Spatie defaults with your custom columns
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'is_system',
    ];

    /**
     * Cast custom columns
     */
    protected $casts = [
        'is_system'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Users assigned to this role
     * Spatie already provides this, but explicit for clarity
     */
    public function users(): BelongsToMany
    {
        $userModel = config('permission.models.user', User::class);

        return $this->morphedByMany(
            $userModel,
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.role_pivot_key', 'role_id'),
            config('permission.column_names.model_morph_key', 'model_id')
        );
    }

    // ═══════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════

    /**
     * Only non-system roles (safe to edit/delete)
     */
    public function scopeEditable($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Only system roles
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════

    /**
     * Check if this role is protected from modification
     */
    public function isProtected(): bool
    {
        return $this->is_system;
    }

    /**
     * Check if role can be safely deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->users()->count() === 0;
    }
}