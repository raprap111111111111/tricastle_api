<?php

// app/Domain/OcrTemplate/Actions/CancelOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class CancelOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(OcrTemplate $ocrTemplate): OcrTemplate
    {
        return $this->repository->cancel($ocrTemplate);
    }
}