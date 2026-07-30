<?php

namespace App\Domain\ApplicantLifestyle\Actions;

use App\Domain\ApplicantLifestyle\Repositories\ApplicantLifestyleRepository;
use App\Models\ApplicantLifestyle;

class DeleteApplicantLifestyleAction
{
    public function __construct(
        private readonly ApplicantLifestyleRepository $repository
    ) {}

    public function execute(ApplicantLifestyle $lifestyle): bool
    {
        return $this->repository->delete($lifestyle->id);
    }
}