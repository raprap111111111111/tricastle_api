<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case UPLOADED = 'uploaded';
    case PENDING_VERIFICATION = 'pending_verification';
    case UNDER_REVIEW = 'under_review';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
    case REQUIRES_CORRECTION = 'requires_correction';

    public function label(): string
    {
        return match($this) {
            self::UPLOADED => 'Uploaded',
            self::PENDING_VERIFICATION => 'Pending Verification',
            self::UNDER_REVIEW => 'Under Review',
            self::VERIFIED => 'Verified',
            self::REJECTED => 'Rejected',
            self::EXPIRED => 'Expired',
            self::REQUIRES_CORRECTION => 'Requires Correction',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UPLOADED => 'secondary',
            self::PENDING_VERIFICATION => 'warning',
            self::UNDER_REVIEW => 'info',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
            self::EXPIRED => 'dark',
            self::REQUIRES_CORRECTION => 'warning',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::UPLOADED => 'upload',
            self::PENDING_VERIFICATION => 'clock',
            self::UNDER_REVIEW => 'search',
            self::VERIFIED => 'check-circle',
            self::REJECTED => 'times-circle',
            self::EXPIRED => 'calendar-times',
            self::REQUIRES_CORRECTION => 'edit',
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
     * Is document approved and ready
     */
    public function isApproved(): bool
    {
        return $this === self::VERIFIED;
    }

    /**
     * Needs action from staff
     */
    public function needsAction(): bool
    {
        return in_array($this, [
            self::PENDING_VERIFICATION,
            self::UNDER_REVIEW,
            self::REQUIRES_CORRECTION,
        ]);
    }

    /**
     * Is document in a rejected/expired state
     */
    public function isProblematic(): bool
    {
        return in_array($this, [
            self::REJECTED,
            self::EXPIRED,
            self::REQUIRES_CORRECTION,
        ]);
    }

    /**
     * Can be re-uploaded
     */
    public function canBeReuploaded(): bool
    {
        return in_array($this, [
            self::REJECTED,
            self::EXPIRED,
            self::REQUIRES_CORRECTION,
        ]);
    }

    /**
     * Get workflow next statuses
     */
    public function nextPossibleStatuses(): array
    {
        return match($this) {
            self::UPLOADED => [self::PENDING_VERIFICATION],
            self::PENDING_VERIFICATION => [self::UNDER_REVIEW, self::REJECTED],
            self::UNDER_REVIEW => [self::VERIFIED, self::REJECTED, self::REQUIRES_CORRECTION],
            self::VERIFIED => [self::EXPIRED, self::REQUIRES_CORRECTION],
            self::REJECTED => [self::UPLOADED],
            self::EXPIRED => [self::UPLOADED],
            self::REQUIRES_CORRECTION => [self::UPLOADED, self::UNDER_REVIEW],
        };
    }
}
