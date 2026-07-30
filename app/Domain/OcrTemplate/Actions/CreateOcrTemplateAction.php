<?php

// app/Domain/OcrTemplate/Actions/CreateOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\DTOs\CreateOcrTemplateDTO;
use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class CreateOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(CreateOcrTemplateDTO $dto): OcrTemplate
    {
        return $this->repository->create($dto);
    }
}