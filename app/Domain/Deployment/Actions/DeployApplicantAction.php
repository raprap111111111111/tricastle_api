<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\DTOs\DeployApplicantDTO;
use App\Domain\Notification\Traits\HasNotifications;
use App\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeployApplicantAction
{
    use HasNotifications;

    public function execute(ApplicantBatch $applicantBatch, DeployApplicantDTO $dto): ApplicantBatch
    {
        // ─── Business rule: Cannot deploy if already deployed ───
        if ($applicantBatch->status === ApplicantBatchStatus::DEPLOYED) {
            throw new RuntimeException('Applicant is already deployed.');
        }

        // ─── Calculate contract end date if not provided ───
        $contractEndDate = $dto->contractEndDate;
        if (! $contractEndDate && $dto->contractStartDate && $dto->contractDurationMonths) {
            $contractEndDate = date(
                'Y-m-d',
                strtotime("+{$dto->contractDurationMonths} months", strtotime($dto->contractStartDate))
            );
        }

        // ─── Update the record ───
        DB::transaction(function () use ($applicantBatch, $dto, $contractEndDate) {
            $applicantBatch->update([
                'status'                   => ApplicantBatchStatus::DEPLOYED->value,
                'deployed_at'              => $dto->deploymentDate,
                'deployment_country'       => $dto->deploymentCountry,
                'deployment_company'       => $dto->deploymentCompany,
                'deployment_position'      => $dto->deploymentPosition,
                'contract_duration_months' => $dto->contractDurationMonths,
                'contract_start_date'      => $dto->contractStartDate,
                'contract_end_date'        => $contractEndDate,
                'monthly_salary'           => $dto->monthlySalary,
                'salary_currency'          => $dto->salaryCurrency,
                'flight_date'              => $dto->flightDate,
                'visa_type'                => $dto->visaType,
                'deployment_notes'         => $dto->deploymentNotes,
                'processed_by'             => $dto->processedBy,
            ]);
        });

        $applicantBatch->refresh()->load([
            'applicant',
            'batch',
            'processedBy',
        ]);

        // ─── 🔔 Notify assigned staff ───
        $applicant = $applicantBatch->applicant;
        if ($applicant?->assigned_staff_id) {
            $this->notifyUser(
                user:      $applicant->assigned_staff_id,
                title:     '🚀 Applicant Deployed',
                message:   "{$applicant->first_name} {$applicant->last_name} was deployed to {$dto->deploymentCountry} ({$dto->deploymentCompany}).",
                module:    'deployment',
                actionUrl: "/deployments/{$applicantBatch->id}",
            );
        }

        return $applicantBatch;
    }
}