<?php

namespace App\Domain\ApplicantTattoo\Actions;

use App\Domain\ApplicantTattoo\Repositories\ApplicantTattooRepository;
use App\Models\ApplicantTattoo;

class ToggleApplicantTattooVisibilityAction
{
    public function __construct(
        private readonly ApplicantTattooRepository $repository
    ) {}

    public function execute(ApplicantTattoo $tattoo): ApplicantTattoo
    {
        return $this->repository->update($tattoo->id, [
            'is_visible' => ! $tattoo->is_visible,
        ]);
    }
}