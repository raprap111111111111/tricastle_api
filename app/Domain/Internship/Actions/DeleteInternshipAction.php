<?php

namespace App\Domain\Internship\Actions;

use App\Models\ApplicantInternship;

class DeleteInternshipAction
{
    public function execute(ApplicantInternship $internship): void
    {
        $internship->delete();
    }
}