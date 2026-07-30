<?php

// app/Domain/OcrTemplate/Actions/RejectOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\DTOs\RejectOcrTemplateDTO;
use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class RejectOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(OcrTemplate $ocrTemplate, RejectOcrTemplateDTO $dto): OcrTemplate
    {
        return $this->repository->reject($ocrTemplate, $dto);
    }
}