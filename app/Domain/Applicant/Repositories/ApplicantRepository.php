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
        'applicantBatches.batch',
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
        'trade_or_occupation',   // search by "welder", "carpenter", etc.
    ];

    protected array $filterable = [
        'status',
        'gender',
        'civil_status',
        'nationality',
        'quality_grade',
        'assigned_staff_id',
        'skill_category',        // skilled | semi_skilled | unskilled
        'jlpt_level',            // N5 | N4 | N3 | N2 | N1
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
        'skill_category',
        'jlpt_level',
        'years_japan_experience',
        'expected_salary',
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

        // ──────────────────────────────────────────────────
        // Status filters
        // ──────────────────────────────────────────────────

        // Exclude statuses (comma-separated)
        // e.g. ?exclude_statuses=rejected,withdrawn
        if ($request->filled('exclude_statuses')) {
            $excluded = array_map(
                'trim',
                explode(',', $request->input('exclude_statuses')),
            );
            $query->whereNotIn('status', $excluded);
        }

        // ──────────────────────────────────────────────────
        // Passport filters
        // ──────────────────────────────────────────────────

        // Passport expiring within X months
        // e.g. ?passport_expiring_within_months=3
        if ($request->filled('passport_expiring_within_months')) {
            $months = (int) $request->input('passport_expiring_within_months');
            $query->whereNotNull('passport_expiry')
                  ->whereDate('passport_expiry', '<=', now()->addMonths($months))
                  ->whereDate('passport_expiry', '>=', now());
        }

        // ──────────────────────────────────────────────────
        // Batch filters
        // ──────────────────────────────────────────────────

        // Filter by batch_id
        // e.g. ?batch_id=5
        if ($request->filled('batch_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', $request->input('batch_id'));
            });
        }

        // Filter by applicant's status within a batch
        // e.g. ?batch_status=deployed
        if ($request->filled('batch_status')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('status', $request->input('batch_status'));
            });
        }

        // ──────────────────────────────────────────────────
        // Location filters
        // ──────────────────────────────────────────────────

        // City — partial match, case-insensitive
        // e.g. ?city=davao
        if ($request->filled('city')) {
            $city = trim($request->input('city'));
            $query->where('city', 'like', '%' . $city . '%');
        }

        // Province — exact match (from dropdown)
        // e.g. ?province=Cebu
        if ($request->filled('province')) {
            $query->where('province', $request->input('province'));
        }

        // Address keyword — searches both current and permanent address
        // e.g. ?address=Brgy+Poblacion
        if ($request->filled('address')) {
            $address = trim($request->input('address'));
            $query->where(function (Builder $q) use ($address) {
                $q->where('current_address',   'like', '%' . $address . '%')
                  ->orWhere('permanent_address', 'like', '%' . $address . '%');
            });
        }

        // ──────────────────────────────────────────────────
        // Japan deployment filters (Phase 1)
        // ──────────────────────────────────────────────────

        // Trade / occupation keyword
        // e.g. ?trade_or_occupation=welder
        if ($request->filled('trade_or_occupation')) {
            $trade = trim($request->input('trade_or_occupation'));
            $query->where('trade_or_occupation', 'like', '%' . $trade . '%');
        }

        // Preferred work location keyword
        // e.g. ?preferred_work_location=osaka
        if ($request->filled('preferred_work_location')) {
            $location = trim($request->input('preferred_work_location'));
            $query->where('preferred_work_location', 'like', '%' . $location . '%');
        }

        // TITP occupation keyword
        // e.g. ?titp_occupation=concrete
        if ($request->filled('titp_occupation')) {
            $titp = trim($request->input('titp_occupation'));
            $query->where('titp_occupation', 'like', '%' . $titp . '%');
        }

        // Years of Japan experience — minimum threshold
        // e.g. ?min_years_japan_experience=2
        if ($request->filled('min_years_japan_experience')) {
            $query->where(
                'years_japan_experience',
                '>=',
                (int) $request->input('min_years_japan_experience'),
            );
        }

        // Expected salary range
        // e.g. ?min_expected_salary=150000&max_expected_salary=300000
        if ($request->filled('min_expected_salary')) {
            $query->where('expected_salary', '>=', (float) $request->input('min_expected_salary'));
        }

        if ($request->filled('max_expected_salary')) {
            $query->where('expected_salary', '<=', (float) $request->input('max_expected_salary'));
        }

        // Boolean deployment flags
        // Only filter when the key is explicitly present in the request.
        // Sending ?willing_to_be_deployed=1 filters for true.
        // Sending ?willing_to_be_deployed=0 filters for false.
        // Omitting the key entirely skips the filter.
        foreach ([
            'understands_basic_english',
            'willing_to_be_deployed',
            'japan_deployment_ready',
            'previous_japan_experience',
            'has_titp_certificate',
            'ssw_eligible',
        ] as $flag) {
            if ($request->has($flag)) {
                $query->where($flag, (bool) $request->input($flag));
            }
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