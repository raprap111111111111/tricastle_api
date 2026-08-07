<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\Repositories\DeploymentRepository;
use App\Models\ApplicantBatch;

class GetDeploymentAction
{
    public function __construct(
        private readonly DeploymentRepository $repository,
    ) {}

    public function execute(int $id): ApplicantBatch
    {
        return $this->repository->findOrFail($id);
    }
}