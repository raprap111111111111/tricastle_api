<?php

// app/Domain/Comment/Actions/CreateCommentAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\DTOs\CreateCommentDTO;
use App\Domain\Comment\Repositories\CommentRepository;
use App\Models\Comment;

class CreateCommentAction
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(CreateCommentDTO $dto): Comment
    {
        return $this->repository->create($dto);
    }
}