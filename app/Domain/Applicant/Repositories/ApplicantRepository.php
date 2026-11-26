<?php

namespace App\Domain\Applicant\Repositories;

use App\Models\Applicant;
use App\Models\Company;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ApplicantRepository extends BaseRepository
{
    protected string $model = Applicant::class;

    protected array $relations = [
        'assignedStaff:id,name',
        'passportOffice:id,region,name,address',
        'applicantBatches.batch.company.category',
        'currentDocuments:id,applicant_id,document_type_id,file_path,file_name,status',
        'currentDocuments.documentType:id,code,name',
    ];

    protected array $searchable = [
        'applicant_code',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'phone',
        'passport_number',
        'city',
        'province',
        'trade_or_occupation',
        'applied_position',
        'birthplace',
        'religion',
    ];

    // Note: Fields handled manually via LIKE in query() are excluded here to prevent duplicate WHERE conditions
    protected array $filterable = [
        'status',
        'gender',
        'civil_status',
        'nationality',
        'quality_grade',
        'assigned_staff_id',
        'passport_issuing_office_id',
        'skill_category',
        'jlpt_level',
        'trade_test_try',
        'blood_type',
        'dominant_hand',
        'province',
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
        'passport_issuing_office_id',
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

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // ─── 1. TiDB-Safe Fast Search (No SOUNDEX) ────────────────────────
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $words  = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            $query->where(function (Builder $q) use ($search, $words) {
                // 1) Match against searchable fields with explicit table prefix
                foreach ($this->searchable as $field) {
                    $q->orWhere("applicants.{$field}", 'LIKE', "%{$search}%");
                }

                // 2) Full name variations
                $q->orWhereRaw("CONCAT_WS(' ', applicants.first_name, NULLIF(applicants.middle_name, ''), applicants.last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT_WS(' ', applicants.last_name, NULLIF(applicants.middle_name, ''), applicants.first_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT_WS(' ', applicants.first_name, applicants.last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT_WS(' ', applicants.last_name, applicants.first_name) LIKE ?", ["%{$search}%"]);

                // 3) Multi-term matching (e.g. "Juan Cruz")
                if (count($words) > 1) {
                    $q->orWhere(function (Builder $sub) use ($words) {
                        foreach ($words as $word) {
                            $sub->where(function (Builder $w) use ($word) {
                                $w->where('applicants.first_name', 'LIKE', "%{$word}%")
                                  ->orWhere('applicants.middle_name', 'LIKE', "%{$word}%")
                                  ->orWhere('applicants.last_name', 'LIKE', "%{$word}%")
                                  ->orWhere('applicants.applicant_code', 'LIKE', "%{$word}%");
                            });
                        }
                    });
                }
            });
        }

        // ─── 2. Status & Exclusions ───────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('applicants.status', $request->input('status'));
        }

        if ($request->filled('exclude_statuses')) {
            $excluded = array_map('trim', explode(',', (string) $request->input('exclude_statuses')));
            $query->whereNotIn('applicants.status', array_filter($excluded));
        }

        // ─── 3. Year Filter (Created At) ──────────────────────────────────
        if ($request->filled('year')) {
            $query->whereYear('applicants.created_at', (int) $request->input('year'));
        }

        // ─── 4. Passport & Office Filters ─────────────────────────────────
        if ($request->filled('passport_expiring_within_months')) {
            $months = (int) $request->input('passport_expiring_within_months');
            $query->whereNotNull('applicants.passport_expiry')
                  ->whereDate('applicants.passport_expiry', '<=', now()->addMonths($months))
                  ->whereDate('applicants.passport_expiry', '>=', now());
        }

        if ($request->filled('passport_issuing_office_id')) {
            $query->where('applicants.passport_issuing_office_id', (int) $request->input('passport_issuing_office_id'));
        }

        // ─── 5. Batch & Group Filters ─────────────────────────────────────
        if ($request->filled('batch_id') && $request->filled('group_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', (int) $request->input('batch_id'))
                  ->where('batch_group_id', (int) $request->input('group_id'));
            });
        } elseif ($request->filled('batch_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', (int) $request->input('batch_id'));
            });
        } elseif ($request->filled('group_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_group_id', (int) $request->input('group_id'));
            });
        }

        if ($request->filled('batch_status')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('status', $request->input('batch_status'));
            });
        }

        // ─── 6. Company & Category Filters ────────────────────────────────
        if ($request->filled('company_id')) {
            $companyId   = (int) $request->input('company_id');
            $company     = Company::withTrashed()->find($companyId);
            $companyName = $company?->name;
            $companyCode = $company?->code;

            $query->where(function (Builder $mainQuery) use ($companyId, $companyName, $companyCode) {
                $mainQuery->whereHas('applicantBatches', function (Builder $q) use ($companyId, $companyName, $companyCode) {
                    $q->where(function (Builder $sub) use ($companyId, $companyName, $companyCode) {
                        $sub->where('deployment_company', (string) $companyId);
                        if ($companyName) {
                            $sub->orWhere('deployment_company', $companyName);
                        }
                        if ($companyCode) {
                            $sub->orWhere('deployment_company', $companyCode);
                        }
                    })->orWhereHas('batch', function (Builder $bq) use ($companyId) {
                        $bq->where('receiving_company_id', $companyId);
                    });
                })->orWhereHas('internships', function (Builder $iq) use ($companyId) {
                    $iq->where('receiving_company_id', $companyId)
                       ->orWhere('accepting_company_id', $companyId)
                       ->orWhere('dispatching_company_id', $companyId);
                });
            });
        }

        if ($request->filled('company_category_id')) {
            $categoryId = (int) $request->input('company_category_id');
            $matchingCompanies = Company::withTrashed()
                ->whereHas('category', fn(Builder $cq) => $cq->where('id', $categoryId))
                ->get(['id', 'name', 'code']);

            $matchingCompanyIds   = $matchingCompanies->pluck('id')->map('strval')->toArray();
            $matchingCompanyNames = $matchingCompanies->pluck('name')->filter()->toArray();
            $matchingCompanyCodes = $matchingCompanies->pluck('code')->filter()->toArray();

            $query->where(function (Builder $mainQuery) use ($categoryId, $matchingCompanyIds, $matchingCompanyNames, $matchingCompanyCodes) {
                $mainQuery->whereHas('applicantBatches', function (Builder $q) use ($categoryId, $matchingCompanyIds, $matchingCompanyNames, $matchingCompanyCodes) {
                    $q->where(function (Builder $sub) use ($matchingCompanyIds, $matchingCompanyNames, $matchingCompanyCodes) {
                        $hasCondition = false;
                        if (!empty($matchingCompanyIds)) {
                            $sub->whereIn('deployment_company', $matchingCompanyIds);
                            $hasCondition = true;
                        }
                        if (!empty($matchingCompanyNames)) {
                            if ($hasCondition) {
                                $sub->orWhereIn('deployment_company', $matchingCompanyNames);
                            } else {
                                $sub->whereIn('deployment_company', $matchingCompanyNames);
                                $hasCondition = true;
                            }
                        }
                        if (!empty($matchingCompanyCodes)) {
                            if ($hasCondition) {
                                $sub->orWhereIn('deployment_company', $matchingCompanyCodes);
                            } else {
                                $sub->whereIn('deployment_company', $matchingCompanyCodes);
                            }
                        }
                    })->orWhereHas('batch.company.category', fn(Builder $cq) => $cq->where('id', $categoryId));
                })->orWhereHas('internships', function (Builder $iq) use ($categoryId, $matchingCompanyIds) {
                    $iq->whereHas('receivingCompany.category', fn(Builder $cq) => $cq->where('id', $categoryId));
                    if (!empty($matchingCompanyIds)) {
                        $iq->orWhereIn('receiving_company_id', $matchingCompanyIds)
                           ->orWhereIn('accepting_company_id', $matchingCompanyIds)
                           ->orWhereIn('dispatching_company_id', $matchingCompanyIds);
                    }
                });
            });
        }

        // ─── 7. Demographics & Address Filters ───────────────────────────
        if ($request->filled('gender')) {
            $query->where('applicants.gender', $request->input('gender'));
        }

        if ($request->filled('civil_status')) {
            $query->where('applicants.civil_status', $request->input('civil_status'));
        }

        if ($request->filled('nationality')) {
            $query->where('applicants.nationality', $request->input('nationality'));
        }

        if ($request->filled('quality_grade')) {
            $query->where('applicants.quality_grade', $request->input('quality_grade'));
        }

        if ($request->filled('assigned_staff_id')) {
            $query->where('applicants.assigned_staff_id', (int) $request->input('assigned_staff_id'));
        }

        if ($request->filled('city')) {
            $query->where('applicants.city', 'LIKE', '%' . trim($request->input('city')) . '%');
        }

        if ($request->filled('province')) {
            $query->where('applicants.province', $request->input('province'));
        }

        if ($request->filled('address')) {
            $address = trim($request->input('address'));
            $query->where(function (Builder $q) use ($address) {
                $q->where('applicants.current_address', 'LIKE', '%' . $address . '%')
                  ->orWhere('applicants.permanent_address', 'LIKE', '%' . $address . '%');
            });
        }

        // ─── 8. Trade & Deployment Filters ───────────────────────────────
        if ($request->filled('applied_position')) {
            $query->where('applicants.applied_position', 'LIKE', '%' . trim($request->input('applied_position')) . '%');
        }

        if ($request->filled('trade_or_occupation')) {
            $query->where('applicants.trade_or_occupation', 'LIKE', '%' . trim($request->input('trade_or_occupation')) . '%');
        }

        if ($request->filled('skill_category')) {
            $query->where('applicants.skill_category', $request->input('skill_category'));
        }

        if ($request->filled('jlpt_level')) {
            $query->where('applicants.jlpt_level', $request->input('jlpt_level'));
        }

        if ($request->filled('trade_test_try')) {
            $query->where('applicants.trade_test_try', $request->input('trade_test_try'));
        }

        if ($request->filled('trade_test_date_from')) {
            $query->whereDate('applicants.trade_test_date', '>=', $request->input('trade_test_date_from'));
        }

        if ($request->filled('trade_test_date_to')) {
            $query->whereDate('applicants.trade_test_date', '<=', $request->input('trade_test_date_to'));
        }

        if ($request->filled('birthplace')) {
            $query->where('applicants.birthplace', 'LIKE', '%' . trim($request->input('birthplace')) . '%');
        }

        if ($request->filled('religion')) {
            $query->where('applicants.religion', $request->input('religion'));
        }

        if ($request->filled('min_english_proficiency')) {
            $query->where('applicants.english_proficiency_pct', '>=', (int) $request->input('min_english_proficiency'));
        }

        if ($request->filled('preferred_work_location')) {
            $query->where('applicants.preferred_work_location', 'LIKE', '%' . trim($request->input('preferred_work_location')) . '%');
        }

        if ($request->filled('titp_occupation')) {
            $query->where('applicants.titp_occupation', 'LIKE', '%' . trim($request->input('titp_occupation')) . '%');
        }

        if ($request->filled('min_years_japan_experience')) {
            $query->where('applicants.years_japan_experience', '>=', (int) $request->input('min_years_japan_experience'));
        }

        if ($request->filled('min_expected_salary')) {
            $query->where('applicants.expected_salary', '>=', (float) $request->input('min_expected_salary'));
        }

        if ($request->filled('max_expected_salary')) {
            $query->where('applicants.expected_salary', '<=', (float) $request->input('max_expected_salary'));
        }

        // ─── 9. Boolean Flags ─────────────────────────────────────────────
        $booleanFlags = [
            'understands_basic_english',
            'willing_to_be_deployed',
            'japan_deployment_ready',
            'previous_japan_experience',
            'has_titp_certificate',
            'ssw_eligible',
        ];

        foreach ($booleanFlags as $flag) {
            if ($request->filled($flag)) {
                $val = filter_var($request->input($flag), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($val !== null) {
                    $query->where("applicants.{$flag}", $val);
                }
            }
        }

        if ($request->filled('has_marucon_contact')) {
            $has = filter_var($request->input('has_marucon_contact'), FILTER_VALIDATE_BOOLEAN);
            if ($has) {
                $query->whereHas('japanContacts', fn(Builder $q) => $q->where('affiliation_type', 'marucon'));
            } else {
                $query->whereDoesntHave('japanContacts', fn(Builder $q) => $q->where('affiliation_type', 'marucon'));
            }
        }

        if ($request->filled('has_japan_contact')) {
            $has = filter_var($request->input('has_japan_contact'), FILTER_VALIDATE_BOOLEAN);
            if ($has) {
                $query->whereHas('japanContacts');
            } else {
                $query->whereDoesntHave('japanContacts');
            }
        }

        return $query;
    }

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

    /**
     * Eager-loads full profile with chronological ordering for education & employment timelines.
     */
    public function findWithFullProfile(int $id): ?Applicant
    {
        return Applicant::with([
            'assignedStaff',
            'reviewer',
            'creator',
            'family',
            'japanContacts',
            'lifestyle',
            'educations' => fn($q) => $q->orderBy('date_started', 'desc'),
            'employments' => fn($q) => $q->orderBy('date_started', 'desc'),
            'tattoos',
            'passportOffice',
            'currentDocuments.documentType',
            'applicantBatches.batch',
            'applicantBatches.processedBy',
        ])->find($id);
    }

    public function bulkMoveToFinalList(array $ids, ?int $reviewedBy = null): int
    {
        return Applicant::whereIn('id', $ids)
            ->whereNotIn('status', ['final_list', 'rejected'])
            ->update([
                'status'          => 'final_list',
                'final_listed_at' => now(),
                'reviewed_by'     => $reviewedBy,
            ]);
    }
}