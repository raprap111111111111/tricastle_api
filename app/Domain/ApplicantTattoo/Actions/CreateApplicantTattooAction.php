<?php

namespace App\Domain\ApplicantTattoo\Actions;

use App\Domain\ApplicantTattoo\DTOs\CreateApplicantTattooDTO;
use App\Domain\ApplicantTattoo\Repositories\ApplicantTattooRepository;
use App\Models\ApplicantTattoo;

class CreateApplicantTattooAction
{
    public function __construct(
        private readonly ApplicantTattooRepository $repository
    ) {}

    public function execute(CreateApplicantTattooDTO $dto): ApplicantTattoo
    {
        return $this->repository->create([
            'applicant_id' => $dto->applicantId,
            'location'     => $dto->location,
            'size'         => $dto->size,
            'description'  => $dto->description,
            'photo_path'   => $dto->photoPath,
            'is_visible'   => $dto->isVisible,
        ]);
    }
}