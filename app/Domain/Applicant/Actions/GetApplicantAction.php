<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Models\Applicant;

class GetApplicantAction
{
    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(int $id): Applicant
    {
        return $this->repository->findOrFail($id);
    }
}