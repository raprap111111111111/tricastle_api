<?php

namespace App\Enums;

enum InternshipStatus: string
{
    case Draft       = 'draft';
    case Pending     = 'pending';
    case Active      = 'active';
    case Completed   = 'completed';
    case Transferred = 'transferred';
    case Cancelled   = 'cancelled';
    case Returned    = 'returned_early';

    public function label(): string
    {
        return match ($this) {
            self::Draft       => 'Draft',
            self::Pending     => 'Pending',
            self::Active      => 'Active',
            self::Completed   => 'Completed',
            self::Transferred => 'Transferred',
            self::Cancelled   => 'Cancelled',
            self::Returned    => 'Returned Early',
        };
    }
}