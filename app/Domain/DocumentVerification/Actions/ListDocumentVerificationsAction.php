<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;

class ListDocumentVerificationsAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}