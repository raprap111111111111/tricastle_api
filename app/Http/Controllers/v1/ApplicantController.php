<?php

namespace App\Http\Controllers\v1;

use App\Domain\Applicant\Actions\AssignApplicantAction;
use App\Domain\Applicant\Actions\CreateApplicantAction;
use App\Domain\Applicant\Actions\DeleteApplicantAction;
use App\Domain\Applicant\Actions\GetApplicantAction;
use App\Domain\Applicant\Actions\ListApplicantsAction;
use App\Domain\Applicant\Actions\TransferApplicantAction;
use App\Domain\Applicant\Actions\UpdateApplicantAction;
use App\Domain\Applicant\Actions\UpdateApplicantStatusAction;
use App\Domain\Applicant\Mappers\ApplicantMapper;
use App\Domain\Applicant\Services\DuplicateDetectionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Applicant\GetAllApplicantRequest;
use App\Http\Requests\v1\Applicant\RejectApplicantRequest;
use App\Http\Requests\v1\Applicant\StoreApplicantRequest;
use App\Http\Requests\v1\Applicant\UpdateApplicantRequest;
use App\Http\Requests\v1\Applicant\UpdateApplicantStatusRequest;
use App\Http\Resources\v1\ApplicantResource;
use App\Models\Applicant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function __construct(
        private readonly DuplicateDetectionService     $duplicateService,
        private readonly ListApplicantsAction          $listAction,
        private readonly GetApplicantAction            $getAction,
        private readonly CreateApplicantAction         $createAction,
        private readonly UpdateApplicantAction         $updateAction,
        private readonly DeleteApplicantAction         $deleteAction,
        private readonly AssignApplicantAction         $assignAction,
        private readonly TransferApplicantAction       $transferAction,
        private readonly UpdateApplicantStatusAction   $updateStatusAction,
    ) {}

    public function index(GetAllApplicantRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            ApplicantResource::class
        );

        return $this->responseSuccess($result, 'Applicants retrieved successfully');
    }

    public function show(Applicant $applicant): JsonResponse
    {
        $applicant->load([
            'assignedStaff',
            'reviewer',                    // ← add
            'creator',
            'lifestyle',
            'educations',
            'employments',
            'tattoos',
            'applicantBatches.batch',      // ← add (direct relation with batch)
            'applicantBatches.processedBy', // ← add
        ]);

        return $this->responseSuccess(
            new ApplicantResource($applicant),
            'Applicant retrieved successfully'
        );
    }

    public function store(StoreApplicantRequest $request): JsonResponse
    {
        $applicant = $this->createAction->execute(
            ApplicantMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantResource($applicant),
            'Applicant created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateApplicantRequest $request, Applicant $applicant): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $applicant,
            ApplicantMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            'Applicant updated successfully'
        );
    }

    public function destroy(Applicant $applicant): JsonResponse
    {
        $this->deleteAction->execute($applicant);

        return $this->responseSuccess(null, 'Applicant deleted successfully');
    }

    // ═══════════════════════════════════════════════════════
    // Status Transitions
    // ═══════════════════════════════════════════════════════

    /**
     * Generic status update endpoint.
     * PATCH /applicants/{id}/status
     */
    public function updateStatus(
        UpdateApplicantStatusRequest $request,
        Applicant $applicant,
    ): JsonResponse {
        $updated = $this->updateStatusAction->execute(
            $applicant,
            ApplicantMapper::fromUpdateStatusRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            "Applicant status updated to {$updated->status->label()}"
        );
    }

    /**
     * Move applicant to final list (approved for batch assignment).
     * PATCH /applicants/{id}/move-to-final-list
     */
    public function moveToFinalList(Request $request, Applicant $applicant): JsonResponse
    {
        $updated = $this->updateStatusAction->execute(
            $applicant,
            ApplicantMapper::forMoveToFinalList($request->user()?->id)
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            'Applicant moved to final list successfully'
        );
    }

    /**
     * Reject applicant with a reason.
     * PATCH /applicants/{id}/reject
     */
    public function reject(RejectApplicantRequest $request, Applicant $applicant): JsonResponse
    {
        $updated = $this->updateStatusAction->execute(
            $applicant,
            ApplicantMapper::fromRejectRequest($request)
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            'Applicant rejected successfully'
        );
    }

    // ═══════════════════════════════════════════════════════
    // Staff Assignment
    // ═══════════════════════════════════════════════════════

    public function assign(Request $request, Applicant $applicant): JsonResponse
    {
        $request->validate([
            'staff_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $updated = $this->assignAction->execute(
            $applicant,
            $request->input('staff_id')
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            'Applicant assigned successfully'
        );
    }

    public function transfer(Request $request, Applicant $applicant): JsonResponse
    {
        $request->validate([
            'to_staff_id' => ['required', 'integer', 'exists:users,id'],
            'reason'      => ['nullable', 'string', 'max:500'],
        ]);

        $updated = $this->transferAction->execute(
            $applicant,
            $request->input('to_staff_id'),
            $request->input('reason')
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            'Applicant transferred successfully'
        );
    }

    // ═══════════════════════════════════════════════════════
    // Duplicate Detection
    // ═══════════════════════════════════════════════════════

    public function checkDuplicates(Request $request): JsonResponse
    {
        $request->validate([
            'email'           => 'nullable|email',
            'first_name'      => 'nullable|string',
            'middle_name'     => 'nullable|string',
            'last_name'       => 'nullable|string',
            'date_of_birth'   => 'nullable|date',
            'passport_number' => 'nullable|string',
            'batch_id'        => 'nullable|integer|exists:batches,id',
            'exclude_id'      => 'nullable|integer',
        ]);

        $duplicates = $this->duplicateService->check(
            data: $request->only([
                'email',
                'first_name',
                'middle_name',
                'last_name',
                'date_of_birth',
                'passport_number',
            ]),
            batchId: $request->input('batch_id'),
            excludeId: $request->input('exclude_id'),
        );

        return $this->responseSuccess([
            'has_duplicates' => count($duplicates) > 0,
            'has_blockers'   => $this->duplicateService->hasBlockers($duplicates),
            'duplicates'     => $duplicates,
        ], count($duplicates) > 0 ? 'Duplicates found' : 'No duplicates');
    }
}
