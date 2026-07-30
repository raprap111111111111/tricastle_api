<?php

namespace App\Http\Controllers\v1;

use App\Domain\Applicant\Actions\AssignApplicantAction;
use App\Domain\Applicant\Actions\CreateApplicantAction;
use App\Domain\Applicant\Actions\DeleteApplicantAction;
use App\Domain\Applicant\Actions\GetApplicantAction;
use App\Domain\Applicant\Actions\ListApplicantsAction;
use App\Domain\Applicant\Actions\TransferApplicantAction;
use App\Domain\Applicant\Actions\UpdateApplicantAction;
use App\Domain\Applicant\Mappers\ApplicantMapper;
use App\Domain\Applicant\Services\DuplicateDetectionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Applicant\GetAllApplicantRequest;
use App\Http\Requests\v1\Applicant\StoreApplicantRequest;
use App\Http\Requests\v1\Applicant\UpdateApplicantRequest;
use App\Http\Resources\v1\ApplicantResource;
use App\Models\Applicant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function __construct(
        private readonly DuplicateDetectionService $duplicateService,
        private readonly ListApplicantsAction    $listAction,
        private readonly GetApplicantAction      $getAction,
        private readonly CreateApplicantAction   $createAction,
        private readonly UpdateApplicantAction   $updateAction,
        private readonly DeleteApplicantAction   $deleteAction,
        private readonly AssignApplicantAction   $assignAction,
        private readonly TransferApplicantAction $transferAction,
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
            'creator',
            'lifestyle',
            'educations',
            'employments',
            'tattoos',
            'batches.company',
        ]);

        $applicant = $this->getAction->execute($applicant->id);

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

    public function assign(Request $request, Applicant $applicant): JsonResponse
    {
        $request->validate([
            'staff_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $updated = $this->assignAction->execute(
            $applicant,
            $request->validated('staff_id')
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
            $request->validated('to_staff_id'),
            $request->validated('reason')
        );

        return $this->responseSuccess(
            new ApplicantResource($updated),
            'Applicant transferred successfully'
        );
    }



    /**
     * Check for duplicates without creating.
     */
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
