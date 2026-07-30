<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;

class ListOcrFieldExtractionsAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(array $params, string $resource): mixed
    {
        return $this->repository->paginate($params, $resource);
    }
}