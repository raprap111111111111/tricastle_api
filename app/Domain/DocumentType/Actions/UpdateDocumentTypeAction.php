<?php

namespace App\Domain\DocumentType\Actions;

use App\Domain\DocumentType\DTOs\UpdateDocumentTypeDTO;
use App\Domain\DocumentType\Repositories\DocumentTypeRepository;
use App\Models\DocumentType;

class UpdateDocumentTypeAction
{
    public function __construct(
        private readonly DocumentTypeRepository $repository
    ) {}

    public function execute(DocumentType $documentType, UpdateDocumentTypeDTO $dto): DocumentType
    {
        return $this->repository->update($documentType->id, array_filter([
            'name'               => $dto->name,
            'code'               => $dto->code,
            'description'        => $dto->description,
            'required_fields'    => $dto->requiredFields,
            'validation_rules'   => $dto->validationRules,
            'is_required'        => $dto->isRequired,
            'is_active'          => $dto->isActive,
            'validity_days'      => $dto->validityDays,
            'expiry_warning_days'=> $dto->expiryWarningDays,
            'category'           => $dto->category,
            'sort_order'         => $dto->sortOrder,
        ], fn ($value) => $value !== null));
    }
}