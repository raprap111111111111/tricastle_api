<?php

namespace App\Domain\QualityScore\Actions;

use App\Domain\QualityScore\Repositories\QualityScoreRepository;
use App\Models\QualityScore;

class DeleteQualityScoreAction
{
    public function __construct(
        private readonly QualityScoreRepository $repository
    ) {}

    public function execute(QualityScore $qualityScore): void
    {
        $this->repository->delete($qualityScore->id);
    }
}