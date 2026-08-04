<?php
// app/Domain/Applicant/DTOs/UpdateStatusDTO.php

namespace App\Domain\Applicant\DTOs;

use App\Enums\ApplicantStatus;

final readonly class UpdateStatusDTO
{
    public function __construct(
        public ApplicantStatus $status,
        public ?string         $rejectionReason = null,
        public ?int            $reviewedBy      = null,
    ) {}
}