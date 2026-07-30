<?php

namespace App\Domain\User\Repositories;

use App\Models\User;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    protected string $model = User::class;

    protected array $relations = ['roles', 'permissions'];
    protected array $searchable = ['first_name', 'last_name', 'email', 'employee_code'];
    protected array $filterable = ['is_active', 'department', 'position'];
    protected array $sortable = [
        'id', 'first_name', 'last_name', 'email',
        'employee_code', 'department', 'position',
        'is_active', 'last_login_at', 'created_at', 'updated_at'
    ];

    protected string $defaultOrderBy = 'id';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query = parent::query();

        $request = request();

        // Filter by role
        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        // Exclude by role
        if ($excludeRole = $request->input('exclude_role')) {
            $query
                ->whereHas('roles')
                ->whereDoesntHave('roles', fn($q) => $q->where('name', $excludeRole));
        }

        return $query;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', $email)->first();
    }
}
