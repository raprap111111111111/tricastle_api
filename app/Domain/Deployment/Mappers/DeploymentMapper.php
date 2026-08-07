<?php

namespace App\Domain\Deployment\Mappers;

use App\Domain\Deployment\DTOs\CancelDeploymentDTO;
use App\Domain\Deployment\DTOs\DeployApplicantDTO;
use App\Domain\Deployment\DTOs\UpdateDeploymentDTO;
use App\Http\Requests\v1\Deployment\BulkDeployRequest;
use App\Http\Requests\v1\Deployment\CancelDeploymentRequest;
use App\Http\Requests\v1\Deployment\DeployApplicantRequest;
use App\Http\Requests\v1\Deployment\UpdateDeploymentRequest;

class DeploymentMapper
{
    public static function fromDeployRequest(DeployApplicantRequest $request): DeployApplicantDTO
    {
        return new DeployApplicantDTO(
            deploymentCountry:      $request->validated('deployment_country'),
            deploymentCompany:      $request->validated('deployment_company'),
            deploymentDate:         $request->validated('deployment_date'),
            deploymentPosition:     $request->validated('deployment_position'),
            contractDurationMonths: $request->validated('contract_duration_months'),
            contractStartDate:      $request->validated('contract_start_date'),
            contractEndDate:        $request->validated('contract_end_date'),
            monthlySalary:          $request->validated('monthly_salary'),
            salaryCurrency:         $request->validated('salary_currency') ?? 'USD',
            flightDate:             $request->validated('flight_date'),
            visaType:               $request->validated('visa_type'),
            deploymentNotes:        $request->validated('deployment_notes'),
            processedBy:            $request->user()?->id,
        );
    }

    public static function fromUpdateRequest(UpdateDeploymentRequest $request): UpdateDeploymentDTO
    {
        return new UpdateDeploymentDTO(
            deploymentCountry:      $request->validated('deployment_country'),
            deploymentCompany:      $request->validated('deployment_company'),
            deploymentPosition:     $request->validated('deployment_position'),
            deploymentDate:         $request->validated('deployment_date'),
            contractDurationMonths: $request->validated('contract_duration_months'),
            contractStartDate:      $request->validated('contract_start_date'),
            contractEndDate:        $request->validated('contract_end_date'),
            monthlySalary:          $request->validated('monthly_salary'),
            salaryCurrency:         $request->validated('salary_currency'),
            flightDate:             $request->validated('flight_date'),
            visaType:               $request->validated('visa_type'),
            deploymentNotes:        $request->validated('deployment_notes'),
            processedBy:            $request->user()?->id,
        );
    }

    public static function fromCancelRequest(CancelDeploymentRequest $request): CancelDeploymentDTO
    {
        return new CancelDeploymentDTO(
            cancellationReason: $request->validated('cancellation_reason'),
            cancelledBy:        $request->user()?->id,
        );
    }

    public static function fromBulkDeployRequest(BulkDeployRequest $request): DeployApplicantDTO
    {
        return new DeployApplicantDTO(
            deploymentCountry:      $request->validated('deployment_country'),
            deploymentCompany:      $request->validated('deployment_company'),
            deploymentDate:         $request->validated('deployment_date'),
            deploymentPosition:     $request->validated('deployment_position'),
            contractDurationMonths: $request->validated('contract_duration_months'),
            contractStartDate:      $request->validated('contract_start_date'),
            contractEndDate:        $request->validated('contract_end_date'),
            monthlySalary:          $request->validated('monthly_salary'),
            salaryCurrency:         $request->validated('salary_currency') ?? 'USD',
            flightDate:             $request->validated('flight_date'),
            visaType:               $request->validated('visa_type'),
            deploymentNotes:        $request->validated('deployment_notes'),
            processedBy:            $request->user()?->id,
        );
    }
}