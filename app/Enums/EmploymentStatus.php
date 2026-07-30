<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case NOT_DEPLOYED = 'not_deployed';
    case IN_TRANSIT = 'in_transit';
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case CONTRACT_ENDING = 'contract_ending';
    case CONTRACT_EXPIRED = 'contract_expired';
    case CONTRACT_RENEWED = 'contract_renewed';
    case TRANSFERRED = 'transferred';
    case RETURNED = 'returned';
    case TERMINATED = 'terminated';
    case ABSCONDED = 'absconded';
    case DECEASED = 'deceased';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match($this) {
            self::NOT_DEPLOYED => 'Not Deployed',
            self::IN_TRANSIT => 'In Transit',
            self::ACTIVE => 'Active - Working',
            self::ON_LEAVE => 'On Leave',
            self::CONTRACT_ENDING => 'Contract Ending Soon',
            self::CONTRACT_EXPIRED => 'Contract Expired',
            self::CONTRACT_RENEWED => 'Contract Renewed',
            self::TRANSFERRED => 'Transferred',
            self::RETURNED => 'Returned to Philippines',
            self::TERMINATED => 'Contract Terminated',
            self::ABSCONDED => 'Absconded (AWOL)',
            self::DECEASED => 'Deceased',
            self::UNKNOWN => 'Status Unknown',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NOT_DEPLOYED => 'secondary',
            self::IN_TRANSIT => 'info',
            self::ACTIVE => 'success',
            self::ON_LEAVE => 'warning',
            self::CONTRACT_ENDING => 'warning',
            self::CONTRACT_EXPIRED => 'danger',
            self::CONTRACT_RENEWED => 'success',
            self::TRANSFERRED => 'info',
            self::RETURNED => 'primary',
            self::TERMINATED => 'danger',
            self::ABSCONDED => 'dark',
            self::DECEASED => 'dark',
            self::UNKNOWN => 'secondary',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::NOT_DEPLOYED => 'home',
            self::IN_TRANSIT => 'plane',
            self::ACTIVE => 'briefcase',
            self::ON_LEAVE => 'umbrella-beach',
            self::CONTRACT_ENDING => 'hourglass-half',
            self::CONTRACT_EXPIRED => 'hourglass-end',
            self::CONTRACT_RENEWED => 'sync',
            self::TRANSFERRED => 'exchange-alt',
            self::RETURNED => 'plane-arrival',
            self::TERMINATED => 'ban',
            self::ABSCONDED => 'user-slash',
            self::DECEASED => 'cross',
            self::UNKNOWN => 'question-circle',
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
     * Is worker currently in destination country?
     */
    public function isCurrentlyDeployed(): bool
    {
        return in_array($this, [
            self::ACTIVE,
            self::ON_LEAVE,
            self::CONTRACT_ENDING,
            self::CONTRACT_RENEWED,
            self::TRANSFERRED,
        ]);
    }

    /**
     * Requires immediate attention
     */
    public function requiresAttention(): bool
    {
        return in_array($this, [
            self::CONTRACT_ENDING,
            self::CONTRACT_EXPIRED,
            self::ABSCONDED,
            self::UNKNOWN,
        ]);
    }

    /**
     * Is worker back in Philippines?
     */
    public function isBackHome(): bool
    {
        return in_array($this, [
            self::RETURNED,
            self::TERMINATED,
        ]);
    }

    /**
     * Critical status requiring alerts
     */
    public function isCritical(): bool
    {
        return in_array($this, [
            self::ABSCONDED,
            self::DECEASED,
            self::CONTRACT_EXPIRED,
            self::UNKNOWN,
        ]);
    }

    /**
     * Get badge class for UI
     */
    public function badge(): string
    {
        return 'badge-' . $this->color();
    }
}
