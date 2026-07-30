<?php

namespace App\Domain\DocumentVersion\Actions;

use App\Domain\DocumentVersion\DTOs\CreateDocumentVersionDTO;
use App\Domain\DocumentVersion\Repositories\DocumentVersionRepository;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;

class CreateDocumentVersionAction
{
    public function __construct(
        private readonly DocumentVersionRepository $repository
    ) {}

    public function execute(CreateDocumentVersionDTO $dto): DocumentVersion
    {
        // ─── Get next version number ───────────────────────────
        $versionNumber = $this->repository->getNextVersionNumber($dto->applicantDocumentId);

        // ─── Store physical file ───────────────────────────────
        $filePath = Storage::disk('local')->putFile(
            'documents/versions/' . date('Y/m'),
            $dto->file
        );

        // ─── Unset current version ─────────────────────────────
        $this->repository->unsetCurrentForDocument($dto->applicantDocumentId);

        // ─── Create new version record ─────────────────────────
        return $this->repository->create([
            'applicant_document_id' => $dto->applicantDocumentId,
            'version_number'        => $versionNumber,
            'file_path'             => $filePath,
            'file_name'             => $dto->file->getClientOriginalName(),
            'file_size'             => $dto->file->getSize(),
            'mime_type'             => $dto->file->getMimeType(),
            'extracted_data'        => $dto->extractedData,
            'change_reason'         => $dto->changeReason,
            'uploaded_by'           => $dto->uploadedBy,
            'is_current'            => true,
        ]);
    }
}