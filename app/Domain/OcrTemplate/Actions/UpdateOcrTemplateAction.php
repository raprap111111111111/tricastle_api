<?php

// app/Domain/OcrTemplate/Actions/UpdateOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\DTOs\UpdateOcrTemplateDTO;
use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class UpdateOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(OcrTemplate $ocrTemplate, UpdateOcrTemplateDTO $dto): OcrTemplate
    {
        return $this->repository->update($ocrTemplate, $dto);
    }
}