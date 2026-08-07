<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\DTOs\UpdateDeploymentDTO;
use App\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateDeploymentAction
{
    public function execute(ApplicantBatch $applicantBatch, UpdateDeploymentDTO $dto): ApplicantBatch
    {
        // ─── Only deployed records can be edited ───
        if ($applicantBatch->status !== ApplicantBatchStatus::DEPLOYED) {
            throw new RuntimeException('Only deployed applicants can be updated.');
        }

        $updateData = array_filter([
            'deployment_country'       => $dto->deploymentCountry,
            'deployment_company'       => $dto->deploymentCompany,
            'deployment_position'      => $dto->deploymentPosition,
            'deployed_at'              => $dto->deploymentDate,
            'contract_duration_months' => $dto->contractDurationMonths,
            'contract_start_date'      => $dto->contractStartDate,
            'contract_end_date'        => $dto->contractEndDate,
            'monthly_salary'           => $dto->monthlySalary,
            'salary_currency'          => $dto->salaryCurrency,
            'flight_date'              => $dto->flightDate,
            'visa_type'                => $dto->visaType,
            'deployment_notes'         => $dto->deploymentNotes,
        ], fn ($v) => $v !== null);

        if (empty($updateData)) {
            return $applicantBatch;
        }

        DB::transaction(function () use ($applicantBatch, $updateData) {
            $applicantBatch->update($updateData);
        });

        return $applicantBatch->refresh()->load([
            'applicant',
            'batch',
            'processedBy',
        ]);
    }
}