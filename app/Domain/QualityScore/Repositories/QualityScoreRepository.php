<?php

namespace App\Domain\QualityScore\Repositories;

use App\Models\QualityScore;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class QualityScoreRepository extends BaseRepository
{
    protected string $model = QualityScore::class;

    protected array $relations = [
        'applicant',
        'calculator',
    ];

    protected array $searchable = [];

    protected array $filterable = [
        'applicant_id',
        'grade',
        'calculated_by',
    ];

    protected array $sortable = [
        'id',
        'overall_score',
        'grade',
        'completeness_score',
        'accuracy_score',
        'consistency_score',
        'timeliness_score',
        'calculated_at',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->filled('min_score')) {
            $query->where('overall_score', '>=', (float) $request->input('min_score'));
        }

        if ($request->filled('max_score')) {
            $query->where('overall_score', '<=', (float) $request->input('max_score'));
        }

        if ($request->boolean('passing_only')) {
            $query->whereIn('grade', ['A', 'B', 'C']);
        }

        if ($request->boolean('failing_only')) {
            $query->where('grade', 'F');
        }

        if ($request->boolean('critical_only')) {
            $query->withCriticalMismatches();
        }

        return $query;
    }

    public function findByApplicant(int $applicantId): ?QualityScore
    {
        return QualityScore::where('applicant_id', $applicantId)
            ->latest()
            ->first();
    }
}