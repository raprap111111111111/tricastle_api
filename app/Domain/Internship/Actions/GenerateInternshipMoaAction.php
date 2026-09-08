<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\DTOs\GenerateMoaDTO;
use App\Domain\Internship\Services\MoaGeneratorService;
use App\Models\ApplicantInternship;
use App\Models\InternshipDocument;
use Illuminate\Validation\ValidationException;

class GenerateInternshipMoaAction
{
    public function __construct(
        private readonly MoaGeneratorService $moaGenerator,
    ) {}

    public function execute(ApplicantInternship $internship, GenerateMoaDTO $dto): InternshipDocument
    {
        $internship->loadMissing('applicant.guarantors');

        if ($internship->applicant->guarantors->count() < 2) {
            throw ValidationException::withMessages([
                'guarantors' => 'Two guarantors are required before generating MOA.',
            ]);
        }

        return $this->moaGenerator->generate($internship, $dto);
    }
}