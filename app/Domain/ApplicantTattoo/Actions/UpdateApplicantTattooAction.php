<?php

namespace App\Domain\ApplicantTattoo\Actions;

use App\Domain\ApplicantTattoo\DTOs\UpdateApplicantTattooDTO;
use App\Domain\ApplicantTattoo\Repositories\ApplicantTattooRepository;
use App\Models\ApplicantTattoo;
use Illuminate\Support\Facades\Storage;

class UpdateApplicantTattooAction
{
    public function __construct(
        private readonly ApplicantTattooRepository $repository
    ) {}

    public function execute(ApplicantTattoo $tattoo, UpdateApplicantTattooDTO $dto): ApplicantTattoo
    {
        // Delete old photo if a new one was uploaded
        if ($dto->photoPath !== null && $tattoo->photo_path) {
            Storage::disk('public')->delete($tattoo->photo_path);
        }

        $payload = array_filter([
            'location'    => $dto->location,
            'size'        => $dto->size,
            'description' => $dto->description,
            'photo_path'  => $dto->photoPath,
            'is_visible'  => $dto->isVisible,
        ], fn ($value) => $value !== null);

        return $this->repository->update($tattoo->id, $payload);
    }
}