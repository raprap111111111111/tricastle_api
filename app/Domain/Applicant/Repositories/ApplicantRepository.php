<?php

namespace App\Domain\Applicant\Repositories;

use App\Models\Applicant;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ApplicantRepository extends BaseRepository
{
    protected string $model = Applicant::class;

    /**
     * ⚡ OPTIMIZED: Highly specific relational scopes for lists.
     * Selects only required batch columns and only columns necessary to stream the profile photo.
     * Bypasses heavy document fields such as extracted_data / validated_data.
     */
    protected array $relations = [
        'assignedStaff:id,name,full_name',
        'applicantBatches.batch:id,batch_number,name,country,is_active',
        'currentDocuments:id,applicant_id,document_type_id,file_path,file_name,status',
        'currentDocuments.documentType:id,code,name',
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
        'trade_or_occupation',
        'applied_position',
        'birthplace',
        'religion',
    ];

    protected array $filterable = [
        'status',
        'gender',
        'civil_status',
        'nationality',
        'quality_grade',
        'assigned_staff_id',
        'skill_category',
        'jlpt_level',
        'applied_position',
        'trade_test_try',
        'blood_type',
        'dominant_hand',
        'religion',
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
        'applied_position',
        'trade_test_date',
        'english_proficiency_pct',
        'date_of_birth',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    // ═══════════════════════════════════════════════════════
    // Base Query
    // ═══════════════════════════════════════════════════════

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // ──────────────────────────────────────────────────
        // 🎯 FULL NAME SEARCH
        // ──────────────────────────────────────────────────

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function (Builder $q) use ($search) {
                // 1. Search standard individual columns
                foreach ($this->searchable as $field) {
                    $q->orWhere($field, 'like', '%' . $search . '%');
                }

                // 2. Search full-name concatenations
                $q->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("CONCAT_WS(' ', last_name, first_name) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("CONCAT_WS(', ', last_name, first_name) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ?", ['%' . $search . '%']);
            });
        }

        // ──────────────────────────────────────────────────
        // Status filters
        // ──────────────────────────────────────────────────

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

        if ($request->filled('passport_expiring_within_months')) {
            $months = (int) $request->input('passport_expiring_within_months');
            $query->whereNotNull('passport_expiry')
                ->whereDate('passport_expiry', '<=', now()->addMonths($months))
                ->whereDate('passport_expiry', '>=', now());
        }

        // ──────────────────────────────────────────────────
        // Batch filters
        // ──────────────────────────────────────────────────

        if ($request->filled('batch_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', $request->input('batch_id'));
            });
        }

        if ($request->filled('batch_status')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('status', $request->input('batch_status'));
            });
        }

        // ──────────────────────────────────────────────────
        // Location filters
        // ──────────────────────────────────────────────────

        if ($request->filled('city')) {
            $city = trim($request->input('city'));
            $query->where('city', 'like', '%' . $city . '%');
        }

        if ($request->filled('province')) {
            $query->where('province', $request->input('province'));
        }

        if ($request->filled('address')) {
            $address = trim($request->input('address'));
            $query->where(function (Builder $q) use ($address) {
                $q->where('current_address',   'like', '%' . $address . '%')
                    ->orWhere('permanent_address', 'like', '%' . $address . '%');
            });
        }

        // ──────────────────────────────────────────────────
        // AIS / Trade Test filters
        // ──────────────────────────────────────────────────

        if ($request->filled('applied_position')) {
            $position = trim($request->input('applied_position'));
            $query->where('applied_position', 'like', '%' . $position . '%');
        }

        if ($request->filled('trade_test_try')) {
            $query->where('trade_test_try', $request->input('trade_test_try'));
        }

        if ($request->filled('trade_test_date_from')) {
            $query->whereDate('trade_test_date', '>=', $request->input('trade_test_date_from'));
        }

        if ($request->filled('trade_test_date_to')) {
            $query->whereDate('trade_test_date', '<=', $request->input('trade_test_date_to'));
        }

        if ($request->filled('birthplace')) {
            $birthplace = trim($request->input('birthplace'));
            $query->where('birthplace', 'like', '%' . $birthplace . '%');
        }

        if ($request->filled('religion')) {
            $query->where('religion', $request->input('religion'));
        }

        if ($request->filled('min_english_proficiency')) {
            $query->where(
                'english_proficiency_pct',
                '>=',
                (int) $request->input('min_english_proficiency'),
            );
        }

        // ──────────────────────────────────────────────────
        // Japan deployment filters
        // ──────────────────────────────────────────────────

        if ($request->filled('trade_or_occupation')) {
            $trade = trim($request->input('trade_or_occupation'));
            $query->where('trade_or_occupation', 'like', '%' . $trade . '%');
        }

        if ($request->filled('preferred_work_location')) {
            $location = trim($request->input('preferred_work_location'));
            $query->where('preferred_work_location', 'like', '%' . $location . '%');
        }

        if ($request->filled('titp_occupation')) {
            $titp = trim($request->input('titp_occupation'));
            $query->where('titp_occupation', 'like', '%' . $titp . '%');
        }

        if ($request->filled('min_years_japan_experience')) {
            $query->where(
                'years_japan_experience',
                '>=',
                (int) $request->input('min_years_japan_experience'),
            );
        }

        if ($request->filled('min_expected_salary')) {
            $query->where('expected_salary', '>=', (float) $request->input('min_expected_salary'));
        }

        if ($request->filled('max_expected_salary')) {
            $query->where('expected_salary', '<=', (float) $request->input('max_expected_salary'));
        }

        // Boolean deployment flags
        foreach (
            [
                'understands_basic_english',
                'willing_to_be_deployed',
                'japan_deployment_ready',
                'previous_japan_experience',
                'has_titp_certificate',
                'ssw_eligible',
            ] as $flag
        ) {
            if ($request->has($flag)) {
                $query->where($flag, (bool) $request->input($flag));
            }
        }

        // ──────────────────────────────────────────────────
        // Japan Contacts filter
        // ──────────────────────────────────────────────────

        if ($request->has('has_marucon_contact')) {
            $has = (bool) $request->input('has_marucon_contact');
            if ($has) {
                $query->whereHas(
                    'japanContacts',
                    fn(Builder $q) => $q->where('affiliation_type', 'marucon')
                );
            } else {
                $query->whereDoesntHave(
                    'japanContacts',
                    fn(Builder $q) => $q->where('affiliation_type', 'marucon')
                );
            }
        }

        if ($request->has('has_japan_contact')) {
            $has = (bool) $request->input('has_japan_contact');
            if ($has) {
                $query->whereHas('japanContacts');
            } else {
                $query->whereDoesntHave('japanContacts');
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

    // ═══════════════════════════════════════════════════════
    // AIS Helpers
    // ═══════════════════════════════════════════════════════

    public function findWithFullProfile(int $id): ?Applicant
    {
        return Applicant::with([
            'assignedStaff',
            'reviewer',
            'creator',
            'family',
            'japanContacts',
            'lifestyle',
            'educations',
            'employments',
            'tattoos',
            'currentDocuments.documentType',
            'applicantBatches.batch',
            'applicantBatches.processedBy',
        ])->find($id);
    }
}