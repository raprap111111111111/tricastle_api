<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REQUIRES_CORRECTION = 'requires_correction';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::REQUIRES_CORRECTION => 'Requires Correction',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'secondary',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'primary',
            self::REQUIRES_CORRECTION => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::IN_PROGRESS => 'spinner',
            self::COMPLETED => 'check',
            self::REQUIRES_CORRECTION => 'edit',
            self::APPROVED => 'check-double',
            self::REJECTED => 'times',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED]);
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::IN_PROGRESS, self::REQUIRES_CORRECTION]);
    }
}
