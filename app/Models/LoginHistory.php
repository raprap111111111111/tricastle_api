<?php
// app/Models/LoginHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class LoginHistory extends Model
{
    use LogsActivity;

    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'location',
        'status',
        'failure_reason',
        'login_method',
        'logged_in_at',
        'logged_out_at',
    ];

    protected $casts = [
        'logged_in_at'  => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    // ═══════════════════════════════════════════════════════
    // 🎯 Spatie Activity Log
    // ═══════════════════════════════════════════════════════
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'ip_address',
                'device_type',
                'browser',
                'platform',
                'location',
                'status',
                'failure_reason',
                'login_method',
                'logged_in_at',
                'logged_out_at',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('LoginHistory')
            ->setDescriptionForEvent(function (string $event) {
                $userName = $this->user?->full_name
                    ?? $this->user?->email
                    ?? "user #{$this->user_id}";

                $ip     = $this->ip_address ?? 'unknown IP';
                $device = $this->device_type
                    ? "on {$this->device_type}"
                    : '';

                // Login attempts
                if ($event === 'created') {
                    return match ($this->status) {
                        'success' => "{$userName} logged in from {$ip} {$device}",
                        'failed'  => "❌ Failed login attempt for {$userName} from {$ip}"
                                    . ($this->failure_reason ? " — {$this->failure_reason}" : ''),
                        default   => "Login recorded for {$userName} from {$ip}",
                    };
                }

                // Logout tracking
                if ($event === 'updated' && $this->isDirty('logged_out_at') && $this->logged_out_at) {
                    return "{$userName} logged out";
                }

                return match ($event) {
                    'updated' => "Login history updated for {$userName}",
                    'deleted' => "Deleted login history record for {$userName}",
                    default   => "Login history {$event} for {$userName}",
                };
            });
    }

    // ═══════════════════════════════════════════════════════
    // Relationships
    // ═══════════════════════════════════════════════════════
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}