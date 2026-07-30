<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\DTOs\CreateCorrectionRequestDTO;
use App\Domain\CorrectionRequest\Notifications\CorrectionRequestedNotification;
use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;
use App\Models\User;

class CreateCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CreateCorrectionRequestDTO $dto): CorrectionRequest
    {
        // ─── Create correction request ─────────────────────────
        $correctionRequest = $this->repository->create([
            'document_verification_id' => $dto->documentVerificationId,
            'applicant_document_id'    => $dto->applicantDocumentId,
            'requested_by'             => $dto->requestedBy,
            'description'              => $dto->description,
            'severity'                 => $dto->severity,
            'status'                   => 'pending',
            'fields_to_correct'        => $dto->fieldsToCorrect,
            'correction_data'          => $dto->correctionData,
            'justification'            => $dto->justification,
            'requires_approval'        => $dto->requiresApproval,
            'requires_new_document'    => $dto->requiresNewDocument,
            'due_date'                 => $dto->dueDate,
        ]);

        // ─── Notify users who can manage correction requests ───
        User::permission('correction-request.viewAny')
            ->get()
            ->each(fn(User $user) => $user->notify(
                new CorrectionRequestedNotification($correctionRequest)
            ));

        return $correctionRequest;
    }
}