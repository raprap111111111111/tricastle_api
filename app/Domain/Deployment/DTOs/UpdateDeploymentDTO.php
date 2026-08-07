<?php

namespace App\Domain\Deployment\DTOs;

final readonly class UpdateDeploymentDTO
{
    public function __construct(
        public ?string $deploymentCountry        = null,
        public ?string $deploymentCompany        = null,
        public ?string $deploymentPosition       = null,
        public ?string $deploymentDate           = null,
        public ?int    $contractDurationMonths   = null,
        public ?string $contractStartDate        = null,
        public ?string $contractEndDate          = null,
        public ?float  $monthlySalary            = null,
        public ?string $salaryCurrency           = null,
        public ?string $flightDate               = null,
        public ?string $visaType                 = null,
        public ?string $deploymentNotes          = null,
        public ?int    $processedBy              = null,
    ) {}
}