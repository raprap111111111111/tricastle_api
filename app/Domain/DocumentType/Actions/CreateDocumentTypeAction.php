<?php

namespace App\Domain\DocumentType\Actions;

use App\Domain\DocumentType\DTOs\CreateDocumentTypeDTO;
use App\Domain\DocumentType\Repositories\DocumentTypeRepository;
use App\Models\DocumentType;

class CreateDocumentTypeAction
{
    public function __construct(
        private readonly DocumentTypeRepository $repository
    ) {}

    public function execute(CreateDocumentTypeDTO $dto): DocumentType
    {
        return $this->repository->create([
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
        ]);
    }
}