<?php

// app/Domain/OcrTemplate/Actions/ApproveOcrTemplateAction.php

namespace App\Domain\OcrTemplate\Actions;

use App\Domain\OcrTemplate\DTOs\ApproveOcrTemplateDTO;
use App\Domain\OcrTemplate\Repositories\OcrTemplateRepository;
use App\Models\OcrTemplate;

class ApproveOcrTemplateAction
{
    public function __construct(
        private readonly OcrTemplateRepository $repository,
    ) {}

    public function execute(OcrTemplate $ocrTemplate, ApproveOcrTemplateDTO $dto): OcrTemplate
    {
        return $this->repository->approve($ocrTemplate, $dto);
    }
}