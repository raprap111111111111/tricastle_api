<?php
// app/Domain/SocialAccount/Repositories/SocialAccountRepository.php

namespace App\Domain\SocialAccount\Repositories;

use App\Models\SocialAccount;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class SocialAccountRepository extends BaseRepository
{
    protected string $model = SocialAccount::class;

    protected array $relations  = ['user'];
    protected array $searchable = ['provider', 'provider_id'];
    protected array $filterable = ['provider', 'user_id'];
    protected array $sortable   = ['created_at'];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query = parent::query();
        $user  = auth()->user();

        // ─── Scope to own records if no viewAny permission ────
        if ($user && !$user->can('social-account.viewAny')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function findByProviderAndId(string $provider, string $providerId): ?SocialAccount
    {
        return SocialAccount::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }
}