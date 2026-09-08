<?php

namespace App\Domain\Internship\Actions;

use App\Domain\Internship\DTOs\GenerateMoaDTO;
use App\Domain\Internship\Services\MoaGeneratorService;
use App\Models\ApplicantInternship;
use Illuminate\Validation\ValidationException;

class BulkGenerateInternshipMoaAction
{
    public function __construct(
        private readonly MoaGeneratorService $moaGenerator,
    ) {}

    public function execute(array $internshipIds, GenerateMoaDTO $dto, ?int $batchId = null, bool $onlyCurrent = true): array
    {
        $query = ApplicantInternship::query()
            ->with(['applicant.guarantors'])
            ->when($internshipIds, fn ($q) => $q->whereIn('id', $internshipIds))
            ->when($batchId, fn ($q) => $q->where('batch_id', $batchId))
            ->when($onlyCurrent, fn ($q) => $q->where('is_current', true));

        $internships = $query->get();

        if ($internships->isEmpty()) {
            throw ValidationException::withMessages([
                'internship_ids' => 'No internships found for MOA generation.',
            ]);
        }

        $missing = $internships->filter(fn ($i) => $i->applicant->guarantors->count() < 2);
        if ($missing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'guarantors' => 'Some applicants are missing 2 guarantors.',
                'applicant_codes' => $missing->pluck('applicant.applicant_code')->values()->all(),
            ]);
        }

        return $this->moaGenerator->generateBulk($internships, $dto);
    }
}