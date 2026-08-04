<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\ActivityLog\Traits\LogsActivity;
use App\Domain\Applicant\DTOs\CreateApplicantDTO;
use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Domain\Applicant\Services\DuplicateDetectionService;
use App\Exceptions\DuplicateApplicantException;
use App\Models\Applicant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateApplicantAction
{
    use LogsActivity;
    public function __construct(
        private readonly ApplicantRepository       $repository,
        private readonly DuplicateDetectionService $duplicateService,
    ) {}

    public function execute(CreateApplicantDTO $dto): Applicant
    {
        return DB::transaction(function () use ($dto) {

            // ── 1. Duplicate detection ──────────────────────
            $duplicates = $this->duplicateService->check(
                data: [
                    'email'           => $dto->email,
                    'first_name'      => $dto->firstName,
                    'middle_name'     => $dto->middleName,
                    'last_name'       => $dto->lastName,
                    'date_of_birth'   => $dto->dateOfBirth,
                    'passport_number' => $dto->passportNumber,
                ],
            );

            if ($this->duplicateService->hasBlockers($duplicates)) {
                $blockers = $this->duplicateService->getBlockers($duplicates);

                Log::warning('Duplicate applicant creation blocked', [
                    'email' => $dto->email,
                    'name'  => "{$dto->firstName} {$dto->lastName}",
                ]);

                throw new DuplicateApplicantException(
                    duplicates: $blockers,
                    message: $blockers[0]['message'],
                );
            }

            // ── 2. Create applicant (always starts as pending) ──
            $applicant = $this->repository->create([
                'first_name'         => $dto->firstName,
                'middle_name'        => $dto->middleName,
                'last_name'          => $dto->lastName,
                'suffix'             => $dto->suffix,
                'email'              => $dto->email,
                'phone'              => $dto->phone,
                'mobile'             => $dto->mobile,
                'date_of_birth'      => $dto->dateOfBirth,
                'gender'             => $dto->gender,
                'civil_status'       => $dto->civilStatus,
                'number_of_children' => $dto->numberOfChildren,
                'nationality'        => $dto->nationality,
                'height_cm'          => $dto->heightCm,
                'weight_kg'          => $dto->weightKg,
                'dominant_hand'      => $dto->dominantHand,
                'blood_type'         => $dto->bloodType,
                'current_address'    => $dto->currentAddress,
                'permanent_address'  => $dto->permanentAddress,
                'city'               => $dto->city,
                'province'           => $dto->province,
                'postal_code'        => $dto->postalCode,
                'passport_number'    => $dto->passportNumber,
                'passport_expiry'    => $dto->passportExpiry,
                'sss_number'         => $dto->sssNumber,
                'tin_number'         => $dto->tinNumber,
                'philhealth_number'  => $dto->philhealthNumber,
                'pagibig_number'     => $dto->pagibigNumber,
                'assigned_staff_id'  => $dto->assignedStaffId,
                'created_by'         => $dto->createdBy,
                'status'             => 'pending', // Always starts as pending
            ]);

            Log::info('Applicant created', [
                'applicant_id' => $applicant->id,
                'email'        => $applicant->email,
                'created_by'   => $dto->createdBy,
            ]);

            return $applicant;
        });
    }
}