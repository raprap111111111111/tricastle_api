<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\Repositories\OcrJobRepository;

class ListOcrJobsAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(array $params, string $resource): mixed
    {
        return $this->repository->paginate($params, $resource);
    }
}