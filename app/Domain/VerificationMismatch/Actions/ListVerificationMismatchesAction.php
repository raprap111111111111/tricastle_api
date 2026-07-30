<?php

namespace App\Domain\VerificationMismatch\Actions;

use App\Domain\VerificationMismatch\Repositories\VerificationMismatchRepository;

class ListVerificationMismatchesAction
{
    public function __construct(
        private readonly VerificationMismatchRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}