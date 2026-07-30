<?php

// app/Domain/Applicant/Actions/GetAllApplicantAction.php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAllApplicantAction
{
    public function __construct(
        private readonly ApplicantRepository $repository,
    ) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($filters);
    }
}
