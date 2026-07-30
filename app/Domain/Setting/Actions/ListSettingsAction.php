<?php

// app/Domain/Setting/Actions/ListSettingsAction.php

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\Repositories\SettingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListSettingsAction
{
    public function __construct(
        private readonly SettingRepository $repository,
    ) {}

    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->list($filters);
    }
}