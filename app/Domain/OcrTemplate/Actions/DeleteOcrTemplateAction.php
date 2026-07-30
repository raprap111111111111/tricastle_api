<?php

// app/Domain/OcrTemplate/Actions/DeleteOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class DeleteOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(OcrTemplate $ocrTemplate): bool
    {
        return $this->repository->delete($ocrTemplate);
    }
}