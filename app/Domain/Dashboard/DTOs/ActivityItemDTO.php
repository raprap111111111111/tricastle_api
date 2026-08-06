<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final class ActivityItemDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $actor,
        public readonly string $icon,
        public readonly string $createdAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'title'       => $this->title,
            'description' => $this->description,
            'actor'       => $this->actor,
            'icon'        => $this->icon,
            'created_at'  => $this->createdAt,
        ];
    }
}