<?php

namespace App\Domain\FileRepository\Actions;

use App\Domain\FileRepository\Repositories\FileRepositoryRepository;

class ListFileRepositoriesAction
{
    public function __construct(
        private readonly FileRepositoryRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}