<?php

namespace App\Domain\VerificationMismatch\Actions;

use App\Domain\VerificationMismatch\DTOs\CreateVerificationMismatchDTO;
use App\Domain\VerificationMismatch\Notifications\MismatchDetectedNotification;
use App\Domain\VerificationMismatch\Repositories\VerificationMismatchRepository;
use App\Models\User;
use App\Models\VerificationMismatch;

class CreateVerificationMismatchAction
{
    public function __construct(
        private readonly VerificationMismatchRepository $repository
    ) {}

    public function execute(CreateVerificationMismatchDTO $dto): VerificationMismatch
    {
        // ─── Create verification mismatch ──────────────────────
        $mismatch = $this->repository->create([
            'document_verification_id' => $dto->documentVerificationId,
            'field_name'               => $dto->fieldName,
            'field_label'              => $dto->fieldLabel,
            'source_value'             => $dto->sourceValue,
            'entered_value'            => $dto->enteredValue,
            'severity'                 => $dto->severity,
            'mismatch_type'            => $dto->mismatchType,
            'status'                   => $dto->status,
        ]);

        // ─── Notify only on critical severity ─────────────────
        if ($dto->severity === 'critical') {
            User::permission('verification-mismatch.viewAny')
                ->get()
                ->each(fn(User $user) => $user->notify(
                    new MismatchDetectedNotification($mismatch)
                ));
        }

        return $mismatch;
    }
}