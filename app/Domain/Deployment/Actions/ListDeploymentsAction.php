<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\Repositories\DeploymentRepository;

class ListDeploymentsAction
{
    public function __construct(
        private readonly DeploymentRepository $repository,
    ) {}

    public function execute(array $params, ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}