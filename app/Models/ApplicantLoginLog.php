<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantLoginLog extends Model
{
    protected $fillable = [
        'applicant_portal_account_id',
        'email',
        'status',
        'ip_address',
        'user_agent',
        'password_was_hashed',
        'attempted_at',
    ];

    protected $casts = [
        'password_was_hashed' => 'boolean',
        'attempted_at'        => 'datetime',
    ];

    public function account() {
        return $this->belongsTo(ApplicantPortalAccount::class, 'applicant_portal_account_id');
    }
}