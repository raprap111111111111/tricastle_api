<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantGuarantor extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'sequence',
        'full_name',
        'age',
        'civil_status',
        'nationality',
        'address',
        'residence_cert_no',
        'residence_cert_issued_at',
        'residence_cert_place',
        'relationship',
    ];

    protected $casts = [
        'sequence'                 => 'integer',
        'age'                      => 'integer',
        'residence_cert_issued_at' => 'date',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}