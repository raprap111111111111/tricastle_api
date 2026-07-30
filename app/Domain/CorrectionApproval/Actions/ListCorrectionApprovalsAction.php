<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;

class ListCorrectionApprovalsAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}