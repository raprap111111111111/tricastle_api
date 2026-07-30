<?php

// app/Domain/Comment/Actions/ListCommentsAction.php

namespace App\Domain\Comment\Actions;

use App\Domain\Comment\Repositories\CommentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCommentsAction
{
    public function __construct(
        private readonly CommentRepository $repository,
    ) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($filters);
    }
}