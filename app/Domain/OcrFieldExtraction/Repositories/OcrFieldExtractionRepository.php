<?php

namespace App\Domain\OcrFieldExtraction\Repositories;

use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class OcrFieldExtractionRepository extends BaseRepository
{
    protected string $model = \App\Models\OcrFieldExtraction::class;

    protected array $relations = [
        'ocrJob',
        'applicantDocument',
        'corrector',
    ];

    protected array $searchable = [
        'field_name',
        'field_label',
        'extracted_value',
        'normalized_value',
        'final_value',
        'validated_value',
    ];

    protected array $filterable = [
        'ocr_job_id',
        'applicant_document_id',
        'field_name',
        'field_type',
        'field_category',
        'confidence_level',
        'status',
        'source',
        'is_required',
        'is_primary_field',
        'passed_validation',
        'has_validation_errors',
        'was_manually_corrected',
        'matches_other_documents',
        'has_conflicts',
        'has_typo_suggestions',
        'corrected_by',
        'page_number',
    ];

    protected array $sortable = [
        'created_at',
        'sort_order',
        'field_name',
        'confidence_score',
        'status',
        'correction_count',
        'corrected_at',
        'page_number',
    ];

    protected string $defaultOrderBy        = 'sort_order';
    protected string $defaultOrderDirection = 'asc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // Filter by minimum confidence score
        if ($request->filled('min_confidence')) {
            $query->where('confidence_score', '>=', $request->input('min_confidence'));
        }

        // Filter by maximum confidence score
        if ($request->filled('max_confidence')) {
            $query->where('confidence_score', '<=', $request->input('max_confidence'));
        }

        // Filter fields that have been corrected at least N times
        if ($request->filled('min_correction_count')) {
            $query->where('correction_count', '>=', $request->input('min_correction_count'));
        }

        // Filter by correction date range
        if ($request->filled('corrected_from')) {
            $query->whereDate('corrected_at', '>=', $request->input('corrected_from'));
        }
        if ($request->filled('corrected_to')) {
            $query->whereDate('corrected_at', '<=', $request->input('corrected_to'));
        }

        // Filter fields that have a final value set
        if ($request->boolean('has_final_value')) {
            $query->whereNotNull('final_value');
        }

        // Filter fields that are still unresolved (review pending)
        if ($request->boolean('unresolved')) {
            $query->whereIn('status', ['requires_review', 'extracted'])
                  ->where('passed_validation', false);
        }

        return $query;
    }
}