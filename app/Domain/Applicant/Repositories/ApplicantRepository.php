<?php

namespace App\Domain\Applicant\Repositories;

use App\Models\Applicant;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ApplicantRepository extends BaseRepository
{
    protected string $model = Applicant::class;

    protected array $relations = [
        'assignedStaff',
        'creator',
        'batches',
    ];

    protected array $searchable = [
        'applicant_code',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'passport_number',
        'city',
        'province',
    ];

    protected array $filterable = [
        'status',
        'gender',
        'civil_status',
        'nationality',
        'quality_grade',
        'assigned_staff_id',
    ];

    protected array $sortable = [
        'id',
        'applicant_code',
        'first_name',
        'last_name',
        'email',
        'status',
        'quality_score',
        'quality_grade',
        'passport_expiry',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'id';
    protected string $defaultOrderDirection = 'desc';

    // ═══════════════════════════════════════════════════════
    // Base Query
    // ═══════════════════════════════════════════════════════
    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // ── Passport expiring within X months ────────────
        if ($request->filled('passport_expiring_within_months')) {
            $months = (int) $request->input('passport_expiring_within_months');
            $query->whereNotNull('passport_expiry')
                  ->whereDate('passport_expiry', '<=', now()->addMonths($months))
                  ->whereDate('passport_expiry', '>=', now());
        }

        // ── Filter by batch_id ───────────────────────────
        if ($request->filled('batch_id')) {
            $query->whereHas('batches', function (Builder $q) use ($request) {
                $q->where('batches.id', $request->input('batch_id'));
            });
        }

        // ── Filter by batch status ───────────────────────
        if ($request->filled('batch_status')) {
            $query->whereHas('batches', function (Builder $q) use ($request) {
                $q->wherePivot('status', $request->input('batch_status'));
            });
        }

        return $query;
    }

    // ═══════════════════════════════════════════════════════
    // Batch-specific Methods
    // ═══════════════════════════════════════════════════════

    /**
     * Attach an applicant to a batch.
     */
    public function attachBatch(Applicant $applicant, int $batchId, array $pivotData = []): void
    {
        $applicant->batches()->attach($batchId, array_merge([
            'status'     => 'applied',
            'applied_at' => now()->toDateString(),
        ], $pivotData));
    }

    /**
     * Detach an applicant from a batch.
     */
    public function detachBatch(Applicant $applicant, int $batchId): void
    {
        $applicant->batches()->detach($batchId);
    }

    /**
     * Update pivot data for an applicant's batch.
     */
    public function updateBatchStatus(
        Applicant $applicant,
        int       $batchId,
        string    $status,
        array     $pivotData = [],
    ): void {
        $applicant->batches()->updateExistingPivot($batchId, array_merge(
            ['status' => $status],
            $pivotData,
        ));
    }

    /**
     * Check if applicant is already in a batch.
     */
    public function isInBatch(Applicant $applicant, int $batchId): bool
    {
        return $applicant->batches()
                         ->where('batches.id', $batchId)
                         ->exists();
    }

    /**
     * Find applicant with full batch details.
     */
    public function findWithBatches(int $id): ?Applicant
    {
        return Applicant::with([
            'assignedStaff',
            'creator',
            'batches',
        ])->find($id);
    }
}