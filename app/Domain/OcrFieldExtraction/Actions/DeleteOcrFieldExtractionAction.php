<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;

class DeleteOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(OcrFieldExtraction $extraction): void
    {
        $this->repository->delete($extraction->id);
    }
}