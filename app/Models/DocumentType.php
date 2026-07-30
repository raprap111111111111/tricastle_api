<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'required_fields',
        'validation_rules',
        'is_required',
        'is_active',
        'validity_days',
        'expiry_warning_days',
        'category',
        'sort_order',
    ];

    protected $casts = [
        'required_fields'    => 'array',
        'validation_rules'   => 'array',
        'is_required'        => 'boolean',
        'is_active'          => 'boolean',
        'validity_days'      => 'integer',
        'expiry_warning_days'=> 'integer',
        'sort_order'         => 'integer',
    ];

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}