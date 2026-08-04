<?php

namespace App\Domain\Applicant\Repositories;

use App\Models\Applicant;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ApplicantRepository extends BaseRepository
{
    protected string $model = Applicant::class;

    // 🔑 Load applicantBatches with nested batch for list views
    protected array $relations = [
        'assignedStaff',
        'creator',
        'applicantBatches.batch',   // ← changed from 'batches'
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

        // ── Exclude statuses (comma-separated) ────────────
        if ($request->filled('exclude_statuses')) {
            $excluded = array_map(
                'trim',
                explode(',', $request->input('exclude_statuses')),
            );
            $query->whereNotIn('status', $excluded);
        }

        // ── Passport expiring within X months ────────────
        if ($request->filled('passport_expiring_within_months')) {
            $months = (int) $request->input('passport_expiring_within_months');
            $query->whereNotNull('passport_expiry')
                  ->whereDate('passport_expiry', '<=', now()->addMonths($months))
                  ->whereDate('passport_expiry', '>=', now());
        }

        // ── Filter by batch_id ───────────────────────────
        if ($request->filled('batch_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', $request->input('batch_id'));
            });
        }

        // ── Filter by batch status ───────────────────────
        if ($request->filled('batch_status')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('status', $request->input('batch_status'));
            });
        }

        return $query;
    }

    // ═══════════════════════════════════════════════════════
    // Batch-specific Methods
    // ═══════════════════════════════════════════════════════

    public function attachBatch(Applicant $applicant, int $batchId, array $pivotData = []): void
    {
        $applicant->applicantBatches()->create(array_merge([
            'batch_id'    => $batchId,
            'status'      => 'assigned',
            'assigned_at' => now(),
        ], $pivotData));
    }

    public function detachBatch(Applicant $applicant, int $batchId): void
    {
        $applicant->applicantBatches()
            ->where('batch_id', $batchId)
            ->delete();
    }

    public function updateBatchStatus(
        Applicant $applicant,
        int       $batchId,
        string    $status,
        array     $pivotData = [],
    ): void {
        $applicant->applicantBatches()
            ->where('batch_id', $batchId)
            ->update(array_merge(['status' => $status], $pivotData));
    }

    public function isInBatch(Applicant $applicant, int $batchId): bool
    {
        return $applicant->applicantBatches()
                         ->where('batch_id', $batchId)
                         ->exists();
    }

    public function findWithBatches(int $id): ?Applicant
    {
        return Applicant::with([
            'assignedStaff',
            'creator',
            'applicantBatches.batch',
        ])->find($id);
    }
}