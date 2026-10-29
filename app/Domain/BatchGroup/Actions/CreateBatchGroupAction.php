<?php

namespace App\Domain\BatchGroup\Actions;

use App\Domain\BatchGroup\DTOs\CreateBatchGroupDTO;
use App\Domain\BatchGroup\Repositories\BatchGroupRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\BatchGroup;

class CreateBatchGroupAction
{
    use HasNotifications;

    public function __construct(
        private readonly BatchGroupRepository $repository
    ) {}

    public function execute(CreateBatchGroupDTO $dto): BatchGroup
    {
        /** @var BatchGroup $group */
        $group = $this->repository->create([
            'batch_id'  => $dto->batchId,
            'name'      => $dto->name,
            'color'     => $dto->color ?? 'blue',
            'max_slots' => $dto->maxSlots,
        ]);

        $this->notifySuccess(
            permissions: 'batchGroup.viewAny',
            title:       '🏷️ Group Created',
            message:     "Group \"{$group->name}\" was created.",
            module:      'batch',
            actionUrl:   "/batches/{$dto->batchId}",
        );

        return $group;
    }
}
