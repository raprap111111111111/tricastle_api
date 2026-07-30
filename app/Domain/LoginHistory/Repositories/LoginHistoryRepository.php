<?php
// app/Domain/LoginHistory/Repositories/LoginHistoryRepository.php

namespace App\Domain\LoginHistory\Repositories;

use App\Models\LoginHistory;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class LoginHistoryRepository extends BaseRepository
{
    protected string $model = LoginHistory::class;

    protected array $relations  = ['user'];
    protected array $searchable = ['ip_address', 'browser', 'platform', 'location', 'device_type'];
    protected array $filterable = ['status', 'login_method', 'user_id'];
    protected array $sortable   = ['logged_in_at', 'created_at'];

    protected string $defaultOrderBy        = 'logged_in_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // ─── Scope to own records if no viewAny permission ────
        $user = auth()->user();
        if ($user && !$user->can('login-history.viewAny')) {
            $query->where('user_id', $user->id);
        }

        // ─── Date range filter ─────────────────────────────────
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('logged_in_at', [
                $request->input('start_date'),
                $request->input('end_date'),
            ]);
        }

        return $query;
    }

    public function recordLogout(LoginHistory $history): LoginHistory
    {
        $history->update(['logged_out_at' => now()]);

        return $history->fresh();
    }
}