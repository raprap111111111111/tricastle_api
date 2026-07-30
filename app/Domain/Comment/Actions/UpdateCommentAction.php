<?php

// app/Domain/Comment/Actions/UpdateCommentAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\DTOs\UpdateCommentDTO;
use App\Domain\Comment\Repositories\CommentRepository;
use App\Models\Comment;

class UpdateCommentAction
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(Comment $comment, UpdateCommentDTO $dto): Comment
    {
        return $this->repository->update($comment, $dto);
    }
}