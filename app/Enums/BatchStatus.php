<?php

namespace App\Enums;

enum BatchStatus: string
{
    case DRAFT     = 'draft';
    case ONGOING   = 'ongoing';
    case DEPLOYED  = 'deployed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::ONGOING   => 'Ongoing',
            self::DEPLOYED  => 'Deployed',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT     => 'gray',
            self::ONGOING   => 'blue',
            self::DEPLOYED  => 'purple',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }
}