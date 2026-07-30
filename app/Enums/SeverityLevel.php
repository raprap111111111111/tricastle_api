<?php

namespace App\Enums;

enum SeverityLevel: string
{
    case LOW = 'low';
    case MODERATE = 'moderate';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MODERATE => 'Moderate',
            self::CRITICAL => 'Critical',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'success',
            self::MODERATE => 'warning',
            self::CRITICAL => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LOW => 'info-circle',
            self::MODERATE => 'exclamation-triangle',
            self::CRITICAL => 'exclamation-circle',
        };
    }

    public function priority(): int
    {
        return match($this) {
            self::LOW => 1,
            self::MODERATE => 2,
            self::CRITICAL => 3,
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }

    /**
     * Requires supervisor approval
     */
    public function requiresApproval(): bool
    {
        return in_array($this, [
            self::MODERATE,
            self::CRITICAL,
        ]);
    }

    /**
     * Requires admin approval (not just supervisor)
     */
    public function requiresAdminApproval(): bool
    {
        return $this === self::CRITICAL;
    }

    /**
     * Requires immediate notification
     */
    public function requiresImmediateAlert(): bool
    {
        return $this === self::CRITICAL;
    }

    /**
     * SLA response time in hours
     */
    public function slaHours(): int
    {
        return match($this) {
            self::LOW => 72,       // 3 days
            self::MODERATE => 24,  // 1 day
            self::CRITICAL => 4,   // 4 hours
        };
    }

    /**
     * Can be auto-approved without human review
     */
    public function canAutoApprove(): bool
    {
        return $this === self::LOW;
    }

    /**
     * Get badge CSS class
     */
    public function badge(): string
    {
        return 'badge-' . $this->color();
    }
}
