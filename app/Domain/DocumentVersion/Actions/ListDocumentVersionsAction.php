<?php

namespace App\Domain\DocumentVersion\Actions;

use App\Domain\DocumentVersion\Repositories\DocumentVersionRepository;

class ListDocumentVersionsAction
{
    public function __construct(
        private readonly DocumentVersionRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}