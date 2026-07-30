<?php

namespace App\Domain\DocumentType\Actions;

use App\Domain\DocumentType\Repositories\DocumentTypeRepository;

class ListDocumentTypesAction
{
    public function __construct(
        private readonly DocumentTypeRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}