<?php

namespace App\Domain\QualityScore\Actions;

use App\Domain\QualityScore\Repositories\QualityScoreRepository;

class ListQualityScoresAction
{
    public function __construct(
        private readonly QualityScoreRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}