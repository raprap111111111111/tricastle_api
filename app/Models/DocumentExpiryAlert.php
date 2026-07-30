<?php
// app/Models/DocumentExpiryAlert.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentExpiryAlert extends Model
{
    protected $fillable = [
        'applicant_document_id',
        'applicant_id',
        'days_until_expiry',
        'alert_type',
        'email_sent',
        'notification_sent',
        'email_sent_at',
        'notification_sent_at',
        'expiry_date',
    ];

    protected $casts = [
        'email_sent'           => 'boolean',
        'notification_sent'    => 'boolean',
        'email_sent_at'        => 'datetime',
        'notification_sent_at' => 'datetime',
        'expiry_date'          => 'date',
    ];

    public function applicantDocument(): BelongsTo
    {
        return $this->belongsTo(ApplicantDocument::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}