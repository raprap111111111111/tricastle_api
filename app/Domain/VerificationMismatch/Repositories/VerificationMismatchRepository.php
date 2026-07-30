<?php

namespace App\Domain\VerificationMismatch\Repositories;

use App\Models\VerificationMismatch;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class VerificationMismatchRepository extends BaseRepository
{
    protected string $model = VerificationMismatch::class;

    protected array $relations = [
        'documentVerification',
        'resolver',
    ];

    protected array $searchable = [
        'field_name',
        'field_label',
        'source_value',
        'entered_value',
        'resolution_notes',
    ];

    protected array $filterable = [
        'document_verification_id',
        'severity',
        'mismatch_type',
        'status',
        'resolved_by',
    ];

    protected array $sortable = [
        'id',
        'field_name',
        'severity',
        'mismatch_type',
        'status',
        'resolved_at',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('unresolved_only')) {
            $query->unresolved();
        }

        if ($request->boolean('resolved_only')) {
            $query->resolved();
        }

        if ($request->boolean('critical_only')) {
            $query->critical();
        }

        if ($request->boolean('escalated_only')) {
            $query->escalated();
        }

        return $query;
    }
}