<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;

class ListApplicantDocumentsAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}