<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;

class GetCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(int $id): CorrectionRequest
    {
        return $this->repository->findOrFail($id);
    }
}