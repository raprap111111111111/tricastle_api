<?php

namespace App\Domain\FileRepository\Repositories;

use App\Models\FileRepository;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class FileRepositoryRepository extends BaseRepository
{
    protected string $model = FileRepository::class;

    protected array $relations = ['uploader'];

    protected array $searchable = [
        'original_name',
        'file_hash',
        'mime_type',
    ];

    protected array $filterable = [
        'disk',
        'storage_driver',
        'mime_type',
        'is_encrypted',
        'uploaded_by',
    ];

    protected array $sortable = [
        'id',
        'original_name',
        'file_size',
        'mime_type',
        'disk',
        'reference_count',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        if ($request->boolean('unused_only')) {
            $query->unused();
        }

        if ($request->boolean('encrypted_only')) {
            $query->encrypted();
        }

        if ($request->filled('min_size')) {
            $query->where('file_size', '>=', (int) $request->input('min_size'));
        }

        if ($request->filled('max_size')) {
            $query->where('file_size', '<=', (int) $request->input('max_size'));
        }

        return $query;
    }

    public function findByHash(string $hash): ?FileRepository
    {
        return FileRepository::where('file_hash', $hash)->first();
    }

    public function existsByHash(string $hash): bool
    {
        return FileRepository::where('file_hash', $hash)->exists();
    }
}