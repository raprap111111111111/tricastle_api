<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens; // 👈 MUST BE SANCTUM
use Illuminate\Notifications\Notifiable;

class ApplicantPortalAccount extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    protected $fillable = ['applicant_id', 'email', 'password', 'status', 'last_login_at'];
    protected $hidden = ['password'];
    protected $casts = ['password' => 'hashed', 'last_login_at' => 'datetime'];

    public function applicant() {
        return $this->belongsTo(Applicant::class);
    }
}