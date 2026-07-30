<?php

namespace App\Domain\FileRepository\Actions;

use App\Domain\FileRepository\Repositories\FileRepositoryRepository;
use App\Models\FileRepository;

class GetFileRepositoryAction
{
    public function __construct(
        private readonly FileRepositoryRepository $repository
    ) {}

    public function execute(int $id): FileRepository
    {
        return $this->repository->findOrFail($id);
    }
}