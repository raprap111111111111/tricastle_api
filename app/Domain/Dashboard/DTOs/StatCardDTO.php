<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DTOs;

final class StatCardDTO
{
    public function __construct(
        public readonly string $label,
        public readonly int|string $value,
        public readonly string $icon,
        public readonly string $variant,
        public readonly float $trend = 0.0,
        public readonly ?string $trendLabel = 'vs last week',
    ) {}

    public static function make(
        string $label,
        int|string $value,
        string $icon,
        string $variant,
        float $trend = 0.0,
    ): self {
        return new self(
            label: $label,
            value: $value,
            icon: $icon,
            variant: $variant,
            trend: $trend,
        );
    }

    public function toArray(): array
    {
        return [
            'label'       => $this->label,
            'value'       => $this->value,
            'icon'        => $this->icon,
            'variant'     => $this->variant,
            'trend'       => $this->trend,
            'trend_label' => $this->trendLabel,
        ];
    }
}