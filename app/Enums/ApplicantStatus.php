<?php

namespace App\Enums;

enum ApplicantStatus: string
{
    case SelfRegistered = 'self_registered';
    case Pending        = 'pending';
    case UnderReview    = 'under_review';
    case Verified       = 'verified';
    case Incomplete     = 'incomplete';
    case FinalList      = 'final_list';
    case Rejected       = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::SelfRegistered => 'Self-Registered',
            self::Pending        => 'Pending',
            self::UnderReview    => 'Under Review',
            self::Verified       => 'Verified',
            self::Incomplete     => 'Incomplete',
            self::FinalList      => 'Final List',
            self::Rejected       => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SelfRegistered => 'purple',
            self::Pending        => 'gray',
            self::UnderReview    => 'blue',
            self::Verified       => 'teal',
            self::Incomplete     => 'orange',
            self::FinalList      => 'green',
            self::Rejected       => 'red',
        };
    }

    /** Can this applicant be assigned to a batch? */
    public function canAssignToBatch(): bool
    {
        return $this === self::FinalList;
    }

    /** Does this self-registered applicant need initial validation by staff? */
    public function needsStaffReview(): bool
    {
        return $this === self::SelfRegistered;
    }
}