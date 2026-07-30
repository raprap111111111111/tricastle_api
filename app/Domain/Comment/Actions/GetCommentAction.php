<?php

// app/Domain/Comment/Actions/GetCommentAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\Repositories\CommentRepository;
use App\Models\Comment;

class GetCommentAction
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(Comment $comment): Comment
    {
        return $this->repository->findWithRelations($comment);
    }
}