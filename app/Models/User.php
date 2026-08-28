<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * IMPORTANT: Tell Spatie to use 'api' guard
     */
    protected $guard_name = 'api';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'full_name',
        'email',
        'phone',
        'mobile',
        'password',
        'avatar',
        'bio',
        'date_of_birth',
        'gender',
        'employee_code',
        'department',
        'position',
        'hired_date',
        'supervisor_id',
        'address',
        'city',
        'province',
        'country',
        'postal_code',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'login_count',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'password_changed_at',
        'failed_login_attempts',
        'locked_until',
        'locale',
        'timezone',
        'theme',
        'preferences',
        'metadata',
        'notes',
        'theme_preference',
        'effects_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'password'                  => 'hashed',
            'is_active'                 => 'boolean',
            'two_factor_enabled'        => 'boolean',
            'date_of_birth'             => 'date',
            'hired_date'                => 'date',
            'last_login_at'             => 'datetime',
            'password_changed_at'       => 'datetime',
            'locked_until'              => 'datetime',
            'preferences'               => 'array',
            'metadata'                  => 'array',
            'two_factor_recovery_codes' => 'array',
            'effects_enabled'           => 'boolean',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(ApplicantDocument::class, 'uploaded_by');
    }

    public function assignedApplicants()
    {
        return $this->hasMany(Applicant::class, 'assigned_staff_id');
    }

    // ========================================
    // ACCESSORS
    // ========================================
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => trim("{$this->first_name} " .
                ($this->middle_name ? "{$this->middle_name} " : '') .
                "{$this->last_name}" .
                ($this->suffix ? " {$this->suffix}" : ''))
        );
    }

    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn() => strtoupper(substr($this->first_name ?? '', 0, 1) . substr($this->last_name ?? '', 0, 1))
        );
    }

    /**
     * Dynamically format avatar URL for R2 / Storage
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                // If stored as a full URL, return directly
                if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                    return $value;
                }

                // Otherwise, construct full URL using R2 disk configuration
                $baseUrl = config('filesystems.disks.r2.url', env('AWS_URL'));
                return $baseUrl ? rtrim($baseUrl, '/') . '/' . ltrim($value, '/') : Storage::disk('r2')->url($value);
            }
        );
    }

    // ========================================
    // SCOPES
    // ========================================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========================================
    // METHODS
    // ========================================
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'login_count'   => $this->login_count + 1
        ]);
    }
}