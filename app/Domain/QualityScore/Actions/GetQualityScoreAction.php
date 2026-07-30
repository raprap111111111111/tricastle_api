<?php

namespace App\Domain\QualityScore\Actions;

use App\Domain\QualityScore\Repositories\QualityScoreRepository;
use App\Models\QualityScore;

class GetQualityScoreAction
{
    public function __construct(
        private readonly QualityScoreRepository $repository
    ) {}

    public function execute(int $id): QualityScore
    {
        return $this->repository->findOrFail($id);
    }
}