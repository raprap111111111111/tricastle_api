<?php

// app/Domain/Setting/Repositories/SettingRepository.php

namespace App\Domain\Setting\Repositories;

use App\Domain\Setting\DTOs\CreateSettingDTO;
use App\Domain\Setting\DTOs\UpdateSettingDTO;
use App\Models\Setting;
use App\Support\Query\BaseRepository;

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