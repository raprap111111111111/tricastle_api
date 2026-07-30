<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantEmployment\Actions\CreateApplicantEmploymentAction;
use App\Domain\ApplicantEmployment\Actions\DeleteApplicantEmploymentAction;
use App\Domain\ApplicantEmployment\Actions\MarkAsCurrentApplicantEmploymentAction;
use App\Domain\ApplicantEmployment\Actions\UpdateApplicantEmploymentAction;
use App\Domain\ApplicantEmployment\Mappers\ApplicantEmploymentMapper;
use App\Domain\ApplicantEmployment\Repositories\ApplicantEmploymentRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantEmployment\DeleteApplicantEmploymentRequest;
use App\Http\Requests\v1\ApplicantEmployment\GetAllApplicantEmploymentRequest;
use App\Http\Requests\v1\ApplicantEmployment\GetApplicantEmploymentRequest;
use App\Http\Requests\v1\ApplicantEmployment\MarkAsCurrentApplicantEmploymentRequest;
use App\Http\Requests\v1\ApplicantEmployment\StoreApplicantEmploymentRequest;
use App\Http\Requests\v1\ApplicantEmployment\UpdateApplicantEmploymentRequest;
use App\Http\Resources\v1\ApplicantEmploymentResource;
use App\Models\ApplicantEmployment;
use Illuminate\Http\JsonResponse;

class ApplicantEmploymentController extends Controller
{
    public function __construct(
        private readonly ApplicantEmploymentRepository $repository,
        private readonly CreateApplicantEmploymentAction $createAction,
        private readonly UpdateApplicantEmploymentAction $updateAction,
        private readonly DeleteApplicantEmploymentAction $deleteAction,
        private readonly MarkAsCurrentApplicantEmploymentAction $markAsCurrentAction,
    ) {}

    public function index(
        GetAllApplicantEmploymentRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            ApplicantEmploymentResource::class
        );

        return response()->json($result);
    }

    public function store(
        StoreApplicantEmploymentRequest $request
    ): JsonResponse {
        $dto        = ApplicantEmploymentMapper::fromStoreRequest($request);
        $employment = $this->createAction->execute($dto);

        return (new ApplicantEmploymentResource($employment->load('applicant')))
            ->additional(['message' => 'Applicant employment created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetApplicantEmploymentRequest $request,
        ApplicantEmployment $applicantEmployment
    ): JsonResponse {
        return (new ApplicantEmploymentResource($applicantEmployment->load('applicant')))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateApplicantEmploymentRequest $request,
        ApplicantEmployment $applicantEmployment
    ): JsonResponse {
        $dto     = ApplicantEmploymentMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($applicantEmployment, $dto);

        return (new ApplicantEmploymentResource($updated->load('applicant')))
            ->additional(['message' => 'Applicant employment updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteApplicantEmploymentRequest $request,
        ApplicantEmployment $applicantEmployment
    ): JsonResponse {
        $this->deleteAction->execute($applicantEmployment);

        return response()->json([
            'message' => 'Applicant employment deleted successfully.',
        ]);
    }

    public function markAsCurrent(
        MarkAsCurrentApplicantEmploymentRequest $request,
        ApplicantEmployment $applicantEmployment
    ): JsonResponse {
        $updated = $this->markAsCurrentAction->execute($applicantEmployment);

        return (new ApplicantEmploymentResource($updated->load('applicant')))
            ->additional(['message' => 'Employment marked as current successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get all employment records for a specific applicant.
     */
    public function listByApplicant(int $applicantId): JsonResponse
    {
        $employments = $this->repository->findByApplicantId($applicantId);

        return ApplicantEmploymentResource::collection($employments)
            ->response()
            ->setStatusCode(200);
    }
}