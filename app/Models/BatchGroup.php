<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'name',
        'color',
        'max_slots',
    ];

    protected $casts = [
        'max_slots' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function applicantBatches(): HasMany
    {
        return $this->hasMany(ApplicantBatch::class, 'batch_group_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(BatchGroup::class);
    }
}
