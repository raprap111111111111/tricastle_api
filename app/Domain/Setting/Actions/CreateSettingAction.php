<?php

// app/Domain/Setting/Actions/CreateSettingAction.php

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\DTOs\CreateSettingDTO;
use App\Domain\Setting\Repositories\SettingRepository;
use App\Models\Setting;

class CreateSettingAction
{
    public function __construct(
        private readonly SettingRepository $repository,
    ) {}

    public function execute(CreateSettingDTO $dto): Setting
    {
        return $this->repository->create($dto);
    }
}