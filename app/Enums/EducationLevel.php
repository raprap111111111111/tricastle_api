<?php

namespace App\Enums;

enum EducationLevel: string
{
    case ELEMENTARY    = 'elementary';
    case HIGH_SCHOOL   = 'high_school';
    case SENIOR_HIGH   = 'senior_high';
    case VOCATIONAL    = 'vocational';
    case COLLEGE       = 'college';
    case POST_GRADUATE = 'post_graduate';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::ELEMENTARY    => 'Elementary',
            self::HIGH_SCHOOL   => 'High School',
            self::SENIOR_HIGH   => 'Senior High School',
            self::VOCATIONAL    => 'Vocational (TESDA)',
            self::COLLEGE       => 'College',
            self::POST_GRADUATE => 'Post Graduate',
        };
    }
}