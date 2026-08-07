<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\Repositories\DeploymentRepository;

class GetDeploymentStatsAction
{
    public function __construct(
        private readonly DeploymentRepository $repository,
    ) {}

    public function execute(): array
    {
        return $this->repository->stats();
    }
}