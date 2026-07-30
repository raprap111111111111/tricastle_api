<?php

// app/Domain/OcrTemplate/Actions/ListOcrTemplatesAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListOcrTemplatesAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($filters);
    }
}