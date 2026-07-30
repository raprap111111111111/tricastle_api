<?php

namespace App\Enums;

enum EducationStatus: string
{
    case GRADUATE      = 'graduate';
    case UNDERGRADUATE = 'undergraduate';
    case ONGOING       = 'ongoing';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::GRADUATE      => 'Graduate',
            self::UNDERGRADUATE => 'Undergraduate',
            self::ONGOING       => 'Ongoing',
        };
    }
}