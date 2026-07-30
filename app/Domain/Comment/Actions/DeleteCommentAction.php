<?php

// app/Domain/Comment/Actions/DeleteCommentAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\Repositories\CommentRepository;
use App\Models\Comment;

class DeleteCommentAction
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(Comment $comment): bool
    {
        return $this->repository->delete($comment);
    }
}