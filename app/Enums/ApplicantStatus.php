<?php

namespace App\Enums;

enum ApplicantStatus: string
{
    case PENDING = 'pending';
    case UNDER_REVIEW = 'under_review';
    case DOCUMENTS_INCOMPLETE = 'documents_incomplete';
    case VERIFIED = 'verified';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case BLACKLISTED = 'blacklisted';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::UNDER_REVIEW => 'Under Review',
            self::DOCUMENTS_INCOMPLETE => 'Documents Incomplete',
            self::VERIFIED => 'Verified',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::BLACKLISTED => 'Blacklisted',
        };
    }

    /**
     * Get color for UI badges
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::UNDER_REVIEW => 'info',
            self::DOCUMENTS_INCOMPLETE => 'warning',
            self::VERIFIED => 'success',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
            self::BLACKLISTED => 'dark',
        };
    }

    /**
     * Get icon for UI
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::UNDER_REVIEW => 'search',
            self::DOCUMENTS_INCOMPLETE => 'exclamation-triangle',
            self::VERIFIED => 'check-circle',
            self::APPROVED => 'check-double',
            self::REJECTED => 'times-circle',
            self::CANCELLED => 'ban',
            self::BLACKLISTED => 'user-slash',
        };
    }

    /**
     * Get all statuses as array for dropdowns
     */
    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }

    /**
     * Check if status is final (cannot change)
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::REJECTED,
            self::CANCELLED,
            self::BLACKLISTED,
        ]);
    }

    /**
     * Check if applicant can be deployed
     */
    public function canBeDeployed(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if applicant is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::UNDER_REVIEW,
            self::DOCUMENTS_INCOMPLETE,
        ]);
    }

    /**
     * Get valid next statuses (workflow)
     */
    public function nextPossibleStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::UNDER_REVIEW, self::CANCELLED],
            self::UNDER_REVIEW => [self::DOCUMENTS_INCOMPLETE, self::VERIFIED, self::REJECTED],
            self::DOCUMENTS_INCOMPLETE => [self::UNDER_REVIEW, self::REJECTED, self::CANCELLED],
            self::VERIFIED => [self::APPROVED, self::REJECTED],
            self::APPROVED => [],
            self::REJECTED => [self::UNDER_REVIEW],
            self::CANCELLED => [self::PENDING],
            self::BLACKLISTED => [],
        };
    }
}
