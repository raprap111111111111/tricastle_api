<?php

namespace App\Domain\ApplicantTattoo\Actions;

use App\Domain\ApplicantTattoo\Repositories\ApplicantTattooRepository;
use App\Models\ApplicantTattoo;
use Illuminate\Support\Facades\Storage;

class DeleteApplicantTattooAction
{
    public function __construct(
        private readonly ApplicantTattooRepository $repository
    ) {}

    public function execute(ApplicantTattoo $tattoo): bool
    {
        // Delete the associated photo file
        if ($tattoo->photo_path) {
            Storage::disk('public')->delete($tattoo->photo_path);
        }

        return $this->repository->delete($tattoo->id);
    }
}