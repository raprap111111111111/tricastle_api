<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;

class DeleteCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CorrectionRequest $correctionRequest): void
    {
        $this->repository->delete($correctionRequest->id);
    }
}