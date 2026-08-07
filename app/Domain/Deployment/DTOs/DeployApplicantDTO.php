<?php

namespace App\Domain\Deployment\DTOs;

final readonly class DeployApplicantDTO
{
    public function __construct(
        public string  $deploymentCountry,
        public string  $deploymentCompany,
        public string  $deploymentDate,          // Y-m-d
        public ?string $deploymentPosition       = null,
        public ?int    $contractDurationMonths   = null,
        public ?string $contractStartDate        = null,
        public ?string $contractEndDate          = null,
        public ?float  $monthlySalary            = null,
        public string  $salaryCurrency           = 'USD',
        public ?string $flightDate               = null,
        public ?string $visaType                 = null,
        public ?string $deploymentNotes          = null,
        public ?int    $processedBy              = null,
    ) {}
}