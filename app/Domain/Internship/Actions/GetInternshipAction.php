<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\Repositories\InternshipRepository;
use App\Models\ApplicantInternship;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetInternshipAction
{
    public function __construct(
        private readonly InternshipRepository $repository,
    ) {}

    public function execute(int $id): ApplicantInternship
    {
        $internship = $this->repository->findWithDetails($id);

        if (! $internship) {
            throw new NotFoundHttpException('Internship not found.');
        }

        return $internship;
    }
}