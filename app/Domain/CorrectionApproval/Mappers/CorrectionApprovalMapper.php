<?php

namespace App\Domain\CorrectionApproval\Mappers;

use App\Domain\CorrectionApproval\DTOs\CreateCorrectionApprovalDTO;
use App\Domain\CorrectionApproval\DTOs\DecideCorrectionApprovalDTO;
use App\Domain\CorrectionApproval\DTOs\UpdateCorrectionApprovalDTO;
use App\Http\Requests\v1\CorrectionApproval\ApproveCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\EscalateCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\RejectCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\StoreCorrectionApprovalRequest;
use App\Http\Requests\v1\CorrectionApproval\UpdateCorrectionApprovalRequest;

class CorrectionApprovalMapper
{
    public static function fromCreateRequest(StoreCorrectionApprovalRequest $request): CreateCorrectionApprovalDTO
    {
        return new CreateCorrectionApprovalDTO(
            correctionRequestId: (int) $request->validated('correction_request_id'),
            approverId:          $request->user()->id,
            approvalLevel:       (int) $request->validated('approval_level', 1),
            decision:            $request->validated('decision', 'pending'),
            comments:            $request->validated('comments'),
            conditions:          $request->validated('conditions'),
        );
    }

    public static function fromUpdateRequest(UpdateCorrectionApprovalRequest $request): UpdateCorrectionApprovalDTO
    {
        return new UpdateCorrectionApprovalDTO(
            comments:      $request->validated('comments'),
            conditions:    $request->validated('conditions'),
            approvalLevel: $request->has('approval_level')
                ? (int) $request->validated('approval_level')
                : null,
        );
    }

    public static function fromApproveRequest(ApproveCorrectionApprovalRequest $request): DecideCorrectionApprovalDTO
    {
        return new DecideCorrectionApprovalDTO(
            decision:   'approved',
            approverId: $request->user()->id,
            comments:   $request->validated('comments'),
            conditions: $request->validated('conditions'),
        );
    }

    public static function fromRejectRequest(RejectCorrectionApprovalRequest $request): DecideCorrectionApprovalDTO
    {
        return new DecideCorrectionApprovalDTO(
            decision:   'rejected',
            approverId: $request->user()->id,
            comments:   $request->validated('comments'),
        );
    }

    public static function fromEscalateRequest(EscalateCorrectionApprovalRequest $request): DecideCorrectionApprovalDTO
    {
        return new DecideCorrectionApprovalDTO(
            decision:   'escalated',
            approverId: $request->user()->id,
            comments:   $request->validated('comments'),
        );
    }
}