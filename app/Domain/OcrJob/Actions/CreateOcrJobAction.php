<?php

namespace App\Domain\OcrJob\Actions;

use App\Domain\OcrJob\DTOs\CreateOcrJobDTO;
use App\Domain\OcrJob\Repositories\OcrJobRepository;
use App\Models\OcrJob;
use Illuminate\Support\Facades\Auth;

class CreateOcrJobAction
{
    public function __construct(
        private readonly OcrJobRepository $repository
    ) {}

    public function execute(CreateOcrJobDTO $dto): OcrJob
    {
        return $this->repository->create([
            'applicant_document_id' => $dto->applicantDocumentId,
            'file_repository_id'    => $dto->fileRepositoryId,
            'ocr_template_id'       => $dto->ocrTemplateId,
            'batch_id'              => $dto->batchId,
            'provider'              => $dto->provider,
            'provider_config'       => $dto->providerConfig,
            'priority'              => $dto->priority,
            'notes'                 => $dto->notes,
            'metadata'              => $dto->metadata,
            'status'                => 'pending',
            'initiated_by'          => Auth::id(),
        ]);
    }
}