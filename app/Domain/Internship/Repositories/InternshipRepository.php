<?php

namespace App\Domain\Internship\Repositories;

use App\Models\ApplicantInternship;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class InternshipRepository extends BaseRepository
{
    protected string $model = ApplicantInternship::class;

    protected array $relations = [
        'applicant:id,applicant_code,first_name,middle_name,last_name,suffix,passport_number,status',
        'batch:id,batch_number,name,country,is_active',
        'program:id,code,name,program_type',
        'receivingCompany:id,code,name',
        'acceptingCompany:id,code,name',
        'dispatchingCompany:id,code,name',
    ];

    protected array $searchable = [
        'job_description',
        'place_of_internship',
        'municipality',
        'change_reason',
    ];

    protected array $filterable = [
        'status',
        'program_type',
        'is_current',
        'applicant_id',
        'batch_id',
        'internship_program_id',
        'receiving_company_id',
        'accepting_company_id',
        'dispatching_company_id',
    ];

    protected array $sortable = [
        'id',
        'status',
        'program_type',
        'contract_start',
        'contract_end',
        'is_current',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query = parent::query();
        $request = request();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function (Builder $q) use ($search) {
                foreach ($this->searchable as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }

                $q->orWhereHas('applicant', function (Builder $aq) use ($search) {
                    $aq->where('applicant_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('applicant_code')) {
            $query->whereHas('applicant', fn (Builder $q) =>
                $q->where('applicant_code', $request->input('applicant_code'))
            );
        }

        if ($request->boolean('only_current')) {
            $query->where('is_current', true);
        }

        if ($request->filled('contract_start_from')) {
            $query->whereDate('contract_start', '>=', $request->input('contract_start_from'));
        }

        if ($request->filled('contract_start_to')) {
            $query->whereDate('contract_start', '<=', $request->input('contract_start_to'));
        }

        return $query;
    }

    public function findWithDetails(int $id): ?ApplicantInternship
    {
        return ApplicantInternship::with([
            'applicant.guarantors',
            'batch',
            'program.dispatchingCompany',
            'program.acceptingCompany',
            'program.defaultReceivingCompany',
            'program.witnessUser',
            'dispatchingCompany',
            'acceptingCompany',
            'receivingCompany',
            'previousInternship.receivingCompany',
            'documents.generator',
            'creator',
        ])->find($id);
    }
}