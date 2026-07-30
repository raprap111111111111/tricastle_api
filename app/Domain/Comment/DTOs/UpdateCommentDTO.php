<?php

// app/Domain/Comment/DTOs/UpdateCommentDTO.php

namespace App\Domain\Comment\DTOs;

final readonly class UpdateCommentDTO
{
    public function __construct(
        public ?string $content = null,
        public ?bool   $isInternal = null,
    ) {}
}