<?php

namespace App\Domain\ApplicantBatch\Repositories;

use App\Models\ApplicantBatch;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ApplicantBatchRepository extends BaseRepository
{
    protected string $model = ApplicantBatch::class;

    protected array $relations = [
        'applicant',
        'batch',
        'processedBy',
    ];

    protected array $searchable = [
        'interview_notes',
        'medical_notes',
        'rejection_reason',
        'remarks',
    ];

    protected array $filterable = [
        'applicant_id',
        'batch_id',
        'status',
        'processed_by',
    ];

    protected array $sortable = [
        'id',
        'applicant_id',
        'batch_id',
        'status',
        'assigned_at',
        'interview_date',
        'medical_date',
        'exam_date',
        'accepted_at',
        'deployed_at',
        'exam_score',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'assigned_at';
    protected string $defaultOrderDirection = 'desc';

    public function findByApplicantId(int $applicantId): Collection
    {
        return $this->query()
            ->where('applicant_id', $applicantId)
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    public function findByBatchId(int $batchId): Collection
    {
        return $this->query()
            ->where('batch_id', $batchId)
            ->orderBy('assigned_at', 'desc')
            ->get();
    }
}