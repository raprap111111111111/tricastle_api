<?php

// app/Domain/Comment/DTOs/CreateCommentDTO.php

namespace App\Domain\Comment\DTOs;

final readonly class CreateCommentDTO
{
    public function __construct(
        public int     $userId,
        public string  $commentableType,
        public int     $commentableId,
        public string  $content,
        public ?int    $parentId = null,
        public bool    $isInternal = true,
    ) {}
}