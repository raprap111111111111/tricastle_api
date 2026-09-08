<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\Repositories\InternshipRepository;

class ListInternshipsAction
{
    public function __construct(
        private readonly InternshipRepository $repository,
    ) {}

    public function execute(array $params, string $resourceClass)
    {
        return $this->repository->paginate($params, $resourceClass);
    }
}