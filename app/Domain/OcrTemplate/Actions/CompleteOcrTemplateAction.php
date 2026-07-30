<?php

// app/Domain/OcrTemplate/Actions/CompleteOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\DTOs\CompleteOcrTemplateDTO;
use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class CompleteOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(OcrTemplate $ocrTemplate, CompleteOcrTemplateDTO $dto): OcrTemplate
    {
        return $this->repository->complete($ocrTemplate, $dto);
    }
}