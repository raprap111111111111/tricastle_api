<?php

namespace App\Enums;

enum TattooSize: string
{
    case SMALL  = 'small';
    case MEDIUM = 'medium';
    case LARGE  = 'large';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::SMALL  => 'Small',
            self::MEDIUM => 'Medium',
            self::LARGE  => 'Large',
        };
    }
}