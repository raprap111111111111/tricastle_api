<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;

class GetOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(int $id): OcrJob
    {
        return $this->repository->findOrFail($id);
    }
}