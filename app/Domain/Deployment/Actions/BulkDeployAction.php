<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\DTOs\DeployApplicantDTO;
use App\Models\ApplicantBatch;
use Throwable;

class BulkDeployAction
{
    public function __construct(
        private readonly DeployApplicantAction $deployAction,
    ) {}

    /**
     * Deploy multiple applicant_batches at once with the same DTO.
     *
     * @param  int[]  $applicantBatchIds
     * @return array{successful: int[], failed: array<int, array{id:int, reason:string}>}
     */
    public function execute(array $applicantBatchIds, DeployApplicantDTO $dto): array
    {
        $successful = [];
        $failed     = [];

        foreach ($applicantBatchIds as $id) {
            try {
                $applicantBatch = ApplicantBatch::findOrFail($id);
                $this->deployAction->execute($applicantBatch, $dto);
                $successful[] = $id;
            } catch (Throwable $e) {
                $failed[] = [
                    'id'     => $id,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return [
            'successful' => $successful,
            'failed'     => $failed,
            'total'      => count($applicantBatchIds),
            'success_count' => count($successful),
            'failed_count'  => count($failed),
        ];
    }
}