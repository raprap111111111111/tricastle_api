<?php

// app/Domain/Setting/Actions/DeleteSettingAction.php

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\Repositories\SettingRepository;
use App\Models\Setting;

class DeleteSettingAction
{
    public function __construct(
        private readonly SettingRepository $repository,
    ) {}

    public function execute(Setting $setting): bool
    {
        return $this->repository->delete($setting);
    }
}