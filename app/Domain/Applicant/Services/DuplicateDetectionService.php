<?php

namespace App\Domain\Applicant\Services;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class DuplicateDetectionService
{
    /**
     * Check for duplicates before creating or updating an applicant.
     *
     * @param  array{
     *   email?:           string,
     *   passport_number?: string,
     *   first_name?:      string,
     *   last_name?:       string,
     *   middle_name?:     string,
     *   date_of_birth?:   string,
     * } $data
     * @param  int|null $batchId    When set, also checks name uniqueness within that batch.
     * @param  int|null $excludeId  Applicant ID to exclude (used on updates so the
     *                              applicant doesn't flag itself as a duplicate).
     * @return array<int, array{type: string, severity: string, message: string, applicant: array}>
     */
    public function check(array $data, ?int $batchId = null, ?int $excludeId = null): array
    {
        $duplicates = [];

        // ── 1. Exact email match — BLOCK ──────────────────
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

        // ── 2. Same passport number — BLOCK ───────────────
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

        // ── 3. Same name within the same batch — BLOCK ────
        // Only runs when a specific batch context is provided
        // (e.g. assigning an applicant to a batch).
        if (
            !empty($data['first_name']) &&
            !empty($data['last_name']) &&
            $batchId !== null
        ) {
            $nameInBatch = $this->findByNameInBatch(
                firstName: $data['first_name'],
                lastName: $data['last_name'],
                middleName: $data['middle_name'] ?? null,
                batchId: $batchId,
                excludeId: $excludeId,
            );

            foreach ($nameInBatch as $match) {
                $duplicates[] = [
                    'type'      => 'name_in_batch',
                    'severity'  => 'block',
                    'message'   => sprintf(
                        "An applicant named '%s %s' already exists in this batch.",
                        $data['first_name'],
                        $data['last_name'],
                    ),
                    'applicant' => $this->formatApplicant($match),
                ];
            }
        }

        // ── 4. Same name + birthdate globally — WARN ──────
        // Warns staff of a possible duplicate person without hard-blocking,
        // because two people can legitimately share a name and birthdate.
        if (
            !empty($data['first_name']) &&
            !empty($data['last_name']) &&
            !empty($data['date_of_birth'])
        ) {
            $sameNameDob = $this->findByNameAndBirthdate(
                firstName: $data['first_name'],
                lastName: $data['last_name'],
                birthdate: $data['date_of_birth'],
                excludeId: $excludeId,
            );

            // Collect IDs already reported above so we don't
            // warn about an applicant we already hard-blocked.
            $alreadyReportedIds = collect($duplicates)
                ->pluck('applicant.id')
                ->filter()
                ->all();

            foreach ($sameNameDob as $match) {
                if (in_array($match->id, $alreadyReportedIds, true)) {
                    continue;
                }

                $duplicates[] = [
                    'type'      => 'similar_person',
                    'severity'  => 'warn',
                    'message'   => sprintf(
                        "Possible duplicate: '%s %s' born on %s already exists in the system.",
                        $data['first_name'],
                        $data['last_name'],
                        $data['date_of_birth'],
                    ),
                    'applicant' => $this->formatApplicant($match),
                ];
            }
        }

        return $duplicates;
    }

    // ═══════════════════════════════════════════════════════
    // Detection Queries
    // ═══════════════════════════════════════════════════════

    private function findByEmail(string $email, ?int $excludeId = null): ?Applicant
    {
        return Applicant::query()
            ->where('email', $email)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->first();
    }

    private function findByPassport(string $passport, ?int $excludeId = null): ?Applicant
    {
        return Applicant::query()
            ->where('passport_number', $passport)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->first();
    }

    /**
     * Find applicants with the same name already assigned to the given batch.
     *
     * Middle name is only matched when provided — avoids false negatives
     * when one record has a middle name and the other doesn't.
     *
     * Bug fix: original used 'batches' relation which goes through the pivot.
     * Changed to 'applicantBatches' to match the actual Applicant model relation.
     *
     * @return EloquentCollection<int, Applicant>
     */
    private function findByNameInBatch(
        string  $firstName,
        string  $lastName,
        ?string $middleName,
        int     $batchId,
        ?int    $excludeId = null,
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
     * Find applicants with the same full name and date of birth.
     *
     * @return EloquentCollection<int, Applicant>
     */
    private function findByNameAndBirthdate(
        string $firstName,
        string $lastName,
        string $birthdate,
        ?int   $excludeId = null,
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

    /**
     * Format an applicant for the duplicates response payload.
     * Includes enough info for the frontend to show a meaningful warning card.
     */
    private function formatApplicant(Applicant $applicant): array
    {
        return [
            'id'             => $applicant->id,
            'applicant_code' => $applicant->applicant_code,
            'full_name'      => trim("{$applicant->first_name} {$applicant->last_name}"),
            'email'          => $applicant->email,
            'status'         => $applicant->status,          // ← added: useful for UI
            'passport_number' => $applicant->passport_number, // ← added: confirm it's same person
            'created_at'     => $applicant->created_at?->toIso8601String(),
        ];
    }

    /**
     * Returns true if any duplicate has severity = 'block'.
     * A blocked result should prevent the applicant from being saved.
     */
    public function hasBlockers(array $duplicates): bool
    {
        return collect($duplicates)
            ->contains(fn($d) => $d['severity'] === 'block');
    }

    /**
     * Returns only the blocking duplicates.
     */
    public function getBlockers(array $duplicates): array
    {
        return collect($duplicates)
            ->where('severity', 'block')
            ->values()
            ->toArray();
    }

    /**
     * Returns only the warning duplicates.
     * Useful when you want to surface soft warnings to the UI
     * without preventing the save.
     */
    public function getWarnings(array $duplicates): array
    {
        return collect($duplicates)
            ->where('severity', 'warn')
            ->values()
            ->toArray();
    }
}
