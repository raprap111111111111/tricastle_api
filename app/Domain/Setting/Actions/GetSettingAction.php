<?php

// app/Domain/Setting/Actions/GetSettingAction.php

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\Repositories\SettingRepository;
use App\Models\Setting;

class GetSettingAction
{
    public function __construct(
        private readonly SettingRepository $repository,
    ) {}

    public function execute(Setting $setting): Setting
    {
        return $this->repository->findWithRelations($setting);
    }
}