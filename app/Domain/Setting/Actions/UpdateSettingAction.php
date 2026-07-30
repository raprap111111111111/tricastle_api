<?php

// app/Domain/Setting/Actions/UpdateSettingAction.php

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\DTOs\UpdateSettingDTO;
use App\Domain\Setting\Repositories\SettingRepository;
use App\Models\Setting;

class UpdateSettingAction
{
    public function __construct(
        private readonly SettingRepository $repository,
    ) {}

    public function execute(Setting $setting, UpdateSettingDTO $dto): Setting
    {
        return $this->repository->update($setting, $dto);
    }
}