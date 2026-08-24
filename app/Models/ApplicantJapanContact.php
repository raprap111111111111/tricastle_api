<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantJapanContact extends Model
{
    protected $table = 'applicant_japan_contacts';

    protected $fillable = [
        'applicant_id',
        'affiliation_type', // marucon | non_marucon
        'name',
        'batch_no',
        'company_name',
        'relation',
        'contact_number',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}