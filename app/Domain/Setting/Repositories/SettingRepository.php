<?php
// app/Domain/Setting/Repositories/SettingRepository.php

namespace App\Domain\Setting\Repositories;

use App\Domain\Setting\DTOs\CreateSettingDTO;
use App\Domain\Setting\DTOs\UpdateSettingDTO;
use App\Models\Setting;
use App\Support\Query\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SettingRepository extends BaseRepository
{
    protected string $model = Setting::class;

    protected array $relations = [];

    protected array $searchable = [
        'key',
        'description',
        'group',
    ];

    protected array $filterable = [
        'group',
        'type',
        'is_public',
    ];

    protected array $sortable = [
        'id',
        'key',
        'group',
        'type',
        'is_public',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'group';
    protected string $defaultOrderDirection = 'asc';

    /**
     * List settings with search, filters, sorting, pagination
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Setting::query();

        // 🔍 Search across key/description/group
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }

        // 🎯 Filter by group
        if (!empty($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        // 🎯 Filter by type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // 🎯 Filter by is_public
        if (isset($filters['is_public'])) {
            $query->where('is_public', (bool) $filters['is_public']);
        }

        // ↕️ Sorting (matches your request: order_by + order_dir)
        $orderBy  = $filters['order_by']  ?? $this->defaultOrderBy;
        $orderDir = $filters['order_dir'] ?? $this->defaultOrderDirection;

        if (in_array($orderBy, $this->sortable, true)) {
            $query->orderBy($orderBy, $orderDir);
        } else {
            $query->orderBy($this->defaultOrderBy, $this->defaultOrderDirection);
        }

        // 📄 Pagination
        $limit = (int) ($filters['limit'] ?? 15);
        return $query->paginate($limit);
    }

    public function createFromDTO(CreateSettingDTO $dto): Setting
    {
        return Setting::create([
            'key'         => $dto->key,
            'value'       => $dto->value,
            'type'        => $dto->type,
            'group'       => $dto->group,
            'description' => $dto->description,
            'is_public'   => $dto->isPublic,
        ]);
    }

    public function updateFromDTO(Setting $setting, UpdateSettingDTO $dto): Setting
    {
        $setting->update(array_filter([
            'key'         => $dto->key,
            'value'       => $dto->value,
            'type'        => $dto->type,
            'group'       => $dto->group,
            'description' => $dto->description,
            'is_public'   => $dto->isPublic,
        ], fn($value) => $value !== null));

        return $setting->refresh();
    }

    public function deleteModel(Setting $setting): bool
    {
        return $setting->delete();
    }

    public function findByKey(string $key): ?Setting
    {
        return Setting::where('key', $key)->first();
    }

    public function getByGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return Setting::where('group', $group)->orderBy('key')->get();
    }

    public function getPublicSettings(): \Illuminate\Database\Eloquent\Collection
    {
        return Setting::where('is_public', true)->orderBy('group')->orderBy('key')->get();
    }
}