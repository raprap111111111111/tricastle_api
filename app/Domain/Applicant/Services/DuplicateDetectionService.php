<?php

namespace App\Domain\Applicant\Services;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class DuplicateDetectionService
{
    /**
     * Check for duplicates before creating an applicant.
     */
    public function check(array $data, ?int $batchId = null, ?int $excludeId = null): array
    {
        $duplicates = [];

        // ── 1. Exact email match ──────────────────────────
        if (!empty($data['email'])) {
            $emailMatch = $this->findByEmail($data['email'], $excludeId);
            if ($emailMatch) {
                $duplicates[] = [
                    'type'      => 'email',
                    'severity'  => 'block',
                    'message'   => "An applicant with email '{$data['email']}' already exists.",
                    'applicant' => $this->formatApplicant($emailMatch),
                ];
            }
        }

        // ── 2. Same passport number ───────────────────────
        if (!empty($data['passport_number'])) {
            $passportMatch = $this->findByPassport($data['passport_number'], $excludeId);
            if ($passportMatch) {
                $duplicates[] = [
                    'type'      => 'passport',
                    'severity'  => 'block',
                    'message'   => "Passport number '{$data['passport_number']}' is already registered.",
                    'applicant' => $this->formatApplicant($passportMatch),
                ];
            }
        }

        // ── 3. Same name + same batch ─────────────────────
        if (
            !empty($data['first_name']) &&
            !empty($data['last_name']) &&
            $batchId !== null
        ) {
            $nameInBatch = $this->findByNameInBatch(
                $data['first_name'],
                $data['last_name'],
                $data['middle_name'] ?? null,
                $batchId,
                $excludeId,
            );

            foreach ($nameInBatch as $match) {
                $duplicates[] = [
                    'type'      => 'name_in_batch',
                    'severity'  => 'block',
                    'message'   => sprintf(
                        "An applicant with the same name (%s %s) already exists in this batch.",
                        $data['first_name'],
                        $data['last_name']
                    ),
                    'applicant' => $this->formatApplicant($match),
                ];
            }
        }

        // ── 4. Same name globally (warning only) ──────────
        if (
            !empty($data['first_name']) &&
            !empty($data['last_name']) &&
            !empty($data['date_of_birth'])
        ) {
            $sameName = $this->findByNameAndBirthdate(
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $excludeId,
            );

            foreach ($sameName as $match) {
                $alreadyReported = collect($duplicates)->contains(
                    fn ($d) => $d['applicant']['id'] === $match->id,
                );
                if ($alreadyReported) continue;

                $duplicates[] = [
                    'type'      => 'similar_person',
                    'severity'  => 'warn',
                    'message'   => sprintf(
                        "Possible duplicate: '%s %s' (born %s) exists in the system.",
                        $data['first_name'],
                        $data['last_name'],
                        $data['date_of_birth']
                    ),
                    'applicant' => $this->formatApplicant($match),
                ];
            }
        }

        return $duplicates;
    }

    // ═══════════════════════════════════════════════════════
    // Detection queries
    // ═══════════════════════════════════════════════════════

    private function findByEmail(string $email, ?int $excludeId = null): ?Applicant
    {
        $query = Applicant::query()->where('email', $email);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    private function findByPassport(string $passport, ?int $excludeId = null): ?Applicant
    {
        $query = Applicant::query()->where('passport_number', $passport);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * @return EloquentCollection<int, Applicant>
     */
    private function findByNameInBatch(
        string $firstName,
        string $lastName,
        ?string $middleName,
        int $batchId,
        ?int $excludeId = null,
    ): EloquentCollection {
        $query = Applicant::query()
            ->where(DB::raw('LOWER(first_name)'), strtolower(trim($firstName)))
            ->where(DB::raw('LOWER(last_name)'), strtolower(trim($lastName)));

        if ($middleName !== null && $middleName !== '') {
            $query->where(
                DB::raw('LOWER(COALESCE(middle_name, ""))'),
                strtolower(trim($middleName))
            );
        }

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $query->whereHas('batches', function ($q) use ($batchId) {
            $q->where('batches.id', $batchId);
        });

        return $query->get();
    }

    /**
     * @return EloquentCollection<int, Applicant>
     */
    private function findByNameAndBirthdate(
        string $firstName,
        string $lastName,
        string $birthdate,
        ?int $excludeId = null,
    ): EloquentCollection {
        $query = Applicant::query()
            ->where(DB::raw('LOWER(first_name)'), strtolower(trim($firstName)))
            ->where(DB::raw('LOWER(last_name)'), strtolower(trim($lastName)))
            ->whereDate('date_of_birth', $birthdate);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    // ═══════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════

    private function formatApplicant(Applicant $applicant): array
    {
        return [
            'id'             => $applicant->id,
            'applicant_code' => $applicant->applicant_code,
            'full_name'      => trim("{$applicant->first_name} {$applicant->last_name}"),
            'email'          => $applicant->email,
            'created_at'     => $applicant->created_at?->toIso8601String(),
        ];
    }

    public function hasBlockers(array $duplicates): bool
    {
        return collect($duplicates)->contains(fn ($d) => $d['severity'] === 'block');
    }

    public function getBlockers(array $duplicates): array
    {
        return collect($duplicates)
            ->where('severity', 'block')
            ->values()
            ->toArray();
    }
}