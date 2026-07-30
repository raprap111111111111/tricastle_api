<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;

class ListCorrectionRequestsAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}