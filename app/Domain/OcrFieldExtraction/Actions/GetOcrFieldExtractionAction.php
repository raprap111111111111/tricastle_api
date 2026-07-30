<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;

class GetOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(int $id): OcrFieldExtraction
    {
        return $this->repository->findOrFail($id);
    }
}