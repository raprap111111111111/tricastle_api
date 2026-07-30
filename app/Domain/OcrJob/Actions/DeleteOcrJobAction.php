<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;

class DeleteOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(OcrJob $ocrJob): void
    {
        $this->repository->delete($ocrJob->id);
    }
}