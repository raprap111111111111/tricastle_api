<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantEducation\Actions\CreateApplicantEducationAction;
use App\Domain\ApplicantEducation\Actions\DeleteApplicantEducationAction;
use App\Domain\ApplicantEducation\Actions\UpdateApplicantEducationAction;
use App\Domain\ApplicantEducation\Mappers\ApplicantEducationMapper;
use App\Domain\ApplicantEducation\Repositories\ApplicantEducationRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantEducation\DeleteApplicantEducationRequest;
use App\Http\Requests\v1\ApplicantEducation\GetAllApplicantEducationRequest;
use App\Http\Requests\v1\ApplicantEducation\GetApplicantEducationRequest;
use App\Http\Requests\v1\ApplicantEducation\StoreApplicantEducationRequest;
use App\Http\Requests\v1\ApplicantEducation\UpdateApplicantEducationRequest;
use App\Http\Resources\v1\ApplicantEducationResource;
use App\Models\ApplicantEducation;
use Illuminate\Http\JsonResponse;

class ApplicantEducationController extends Controller
{
    public function __construct(
        private readonly ApplicantEducationRepository $repository,
        private readonly CreateApplicantEducationAction $createAction,
        private readonly UpdateApplicantEducationAction $updateAction,
        private readonly DeleteApplicantEducationAction $deleteAction,
    ) {}

    public function index(
        GetAllApplicantEducationRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            ApplicantEducationResource::class
        );

        return response()->json($result);
    }

    public function store(
        StoreApplicantEducationRequest $request
    ): JsonResponse {
        $dto       = ApplicantEducationMapper::fromStoreRequest($request);
        $education = $this->createAction->execute($dto);

        return (new ApplicantEducationResource($education->load('applicant')))
            ->additional(['message' => 'Applicant education created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetApplicantEducationRequest $request,
        ApplicantEducation $applicantEducation
    ): JsonResponse {
        return (new ApplicantEducationResource($applicantEducation->load('applicant')))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateApplicantEducationRequest $request,
        ApplicantEducation $applicantEducation
    ): JsonResponse {
        $dto     = ApplicantEducationMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($applicantEducation, $dto);

        return (new ApplicantEducationResource($updated->load('applicant')))
            ->additional(['message' => 'Applicant education updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteApplicantEducationRequest $request,
        ApplicantEducation $applicantEducation
    ): JsonResponse {
        $this->deleteAction->execute($applicantEducation);

        return response()->json([
            'message' => 'Applicant education deleted successfully.',
        ]);
    }

    /**
     * Get all education records for a specific applicant.
     */
    public function listByApplicant(int $applicantId): JsonResponse
    {
        $educations = $this->repository->findByApplicantId($applicantId);

        return ApplicantEducationResource::collection($educations)
            ->response()
            ->setStatusCode(200);
    }
}