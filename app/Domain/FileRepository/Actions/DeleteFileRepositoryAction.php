<?php

namespace App\Domain\FileRepository\Actions;

use App\Domain\FileRepository\Repositories\FileRepositoryRepository;
use App\Models\FileRepository;

class DeleteFileRepositoryAction
{
    public function __construct(
        private readonly FileRepositoryRepository $repository
    ) {}

    public function execute(FileRepository $file): void
    {
        // Decrement reference count first
        $file->decrementReferenceCount();

        // Only soft delete if no more references
        if ($file->isUnused()) {
            $this->repository->delete($file->id);
        }
    }
}