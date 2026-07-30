<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Models\ApplicantDocument;

class GetApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(int $id): ApplicantDocument
    {
        return $this->repository->findOrFail($id);
    }
}