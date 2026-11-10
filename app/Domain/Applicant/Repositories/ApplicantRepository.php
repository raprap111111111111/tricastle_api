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
        'assignedStaff:id,name,full_name',
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
        'passport_issuing_office_id',
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
        'passport_issuing_office_id', // Added Sort Option
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

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchable as $field) {
                    $q->orWhere($field, 'like', '%' . $search . '%');
                }

                $q->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT_WS(' ', last_name, first_name) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT_WS(', ', last_name, first_name) LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ?", ['%' . $search . '%']);
            });
        }

        if ($request->filled('exclude_statuses')) {
            $excluded = array_map(
                'trim',
                explode(',', $request->input('exclude_statuses')),
            );
            $query->whereNotIn('status', $excluded);
        }

        if ($request->filled('passport_expiring_within_months')) {
            $months = (int) $request->input('passport_expiring_within_months');
            $query->whereNotNull('passport_expiry')
                ->whereDate('passport_expiry', '<=', now()->addMonths($months))
                ->whereDate('passport_expiry', '>=', now());
        }

        // Dynamic Filtering by specific DFA Locations
        if ($request->filled('passport_issuing_office_id')) {
            $query->where('passport_issuing_office_id', $request->input('passport_issuing_office_id'));
        }

        if ($request->filled('batch_id') && $request->filled('group_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', $request->input('batch_id'))
                    ->where('batch_group_id', $request->input('group_id'));
            });
        } elseif ($request->filled('batch_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_id', $request->input('batch_id'));
            });
        } elseif ($request->filled('group_id')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('batch_group_id', $request->input('group_id'));
            });
        }

        // ── Company Filter (checks applicantBatches by ID/Name/Code AND internships) ──
        if ($request->filled('company_id')) {
            $companyId   = $request->input('company_id');
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

        // ── Company Category Filter ──
        if ($request->filled('company_category_id')) {
            $categoryId = $request->input('company_category_id');

            // Query matching companies via Eloquent category relation (avoids unknown column errors)
            $matchingCompanies = Company::withTrashed()
                ->whereHas('category', function (Builder $cq) use ($categoryId) {
                    $cq->where('id', $categoryId);
                })
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
                    })->orWhereHas('batch.company.category', function (Builder $cq) use ($categoryId) {
                        $cq->where('id', $categoryId);
                    });
                })->orWhereHas('internships', function (Builder $iq) use ($categoryId, $matchingCompanyIds) {
                    $iq->whereHas('receivingCompany.category', function (Builder $cq) use ($categoryId) {
                        $cq->where('id', $categoryId);
                    });

                    if (!empty($matchingCompanyIds)) {
                        $iq->orWhereIn('receiving_company_id', $matchingCompanyIds)
                            ->orWhereIn('accepting_company_id', $matchingCompanyIds)
                            ->orWhereIn('dispatching_company_id', $matchingCompanyIds);
                    }
                });
            });
        }

        if ($request->filled('batch_status')) {
            $query->whereHas('applicantBatches', function (Builder $q) use ($request) {
                $q->where('status', $request->input('batch_status'));
            });
        }

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
            'passportOffice', // Full Profile Include
            'currentDocuments.documentType',
            'applicantBatches.batch',
            'applicantBatches.processedBy',
        ])->find($id);
    }
}
