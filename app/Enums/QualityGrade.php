<?php

namespace App\Enums;

enum QualityGrade: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case F = 'F';

    public function label(): string
    {
        return match($this) {
            self::A => 'Excellent',
            self::B => 'Good',
            self::C => 'Average',
            self::D => 'Below Average',
            self::F => 'Poor',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::A => '90-100% Quality Score',
            self::B => '80-89% Quality Score',
            self::C => '70-79% Quality Score',
            self::D => '60-69% Quality Score',
            self::F => 'Below 60% Quality Score',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::A => 'success',
            self::B => 'primary',
            self::C => 'info',
            self::D => 'warning',
            self::F => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::A => 'star',
            self::B => 'thumbs-up',
            self::C => 'check',
            self::D => 'exclamation-triangle',
            self::F => 'times-circle',
        };
    }

    public function minScore(): float
    {
        return match($this) {
            self::A => 90.0,
            self::B => 80.0,
            self::C => 70.0,
            self::D => 60.0,
            self::F => 0.0,
        };
    }

    public function maxScore(): float
    {
        return match($this) {
            self::A => 100.0,
            self::B => 89.99,
            self::C => 79.99,
            self::D => 69.99,
            self::F => 59.99,
        };
    }

    public static function fromScore(float $score): self
    {
        return match(true) {
            $score >= 90 => self::A,
            $score >= 80 => self::B,
            $score >= 70 => self::C,
            $score >= 60 => self::D,
            default => self::F,
        };
    }

    public function isPassing(): bool
    {
        return in_array($this, [self::A, self::B, self::C]);
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => "{$case->value} - {$case->label()}"],
            self::cases()
        );
    }
}
