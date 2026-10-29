<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantProfileUpdate extends Model
{
    protected $fillable = ['applicant_id', 'proposed_payload', 'status', 'decline_reason', 'reviewed_by', 'reviewed_at'];
    protected $casts = ['proposed_payload' => 'array', 'reviewed_at' => 'datetime'];

    public function applicant() {
        return $this->belongsTo(Applicant::class);
    }
    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
