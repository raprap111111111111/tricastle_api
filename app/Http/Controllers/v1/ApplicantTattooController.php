<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantTattoo\Actions\CreateApplicantTattooAction;
use App\Domain\ApplicantTattoo\Actions\DeleteApplicantTattooAction;
use App\Domain\ApplicantTattoo\Actions\ToggleApplicantTattooVisibilityAction;
use App\Domain\ApplicantTattoo\Actions\UpdateApplicantTattooAction;
use App\Domain\ApplicantTattoo\Mappers\ApplicantTattooMapper;
use App\Domain\ApplicantTattoo\Repositories\ApplicantTattooRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantTattoo\DeleteApplicantTattooRequest;
use App\Http\Requests\v1\ApplicantTattoo\GetAllApplicantTattooRequest;
use App\Http\Requests\v1\ApplicantTattoo\GetApplicantTattooRequest;
use App\Http\Requests\v1\ApplicantTattoo\StoreApplicantTattooRequest;
use App\Http\Requests\v1\ApplicantTattoo\ToggleApplicantTattooVisibilityRequest;
use App\Http\Requests\v1\ApplicantTattoo\UpdateApplicantTattooRequest;
use App\Http\Resources\v1\ApplicantTattooResource;
use App\Models\ApplicantTattoo;
use Illuminate\Http\JsonResponse;

class ApplicantTattooController extends Controller
{
    public function __construct(
        private readonly ApplicantTattooRepository $repository,
        private readonly CreateApplicantTattooAction $createAction,
        private readonly UpdateApplicantTattooAction $updateAction,
        private readonly DeleteApplicantTattooAction $deleteAction,
        private readonly ToggleApplicantTattooVisibilityAction $toggleVisibilityAction,
    ) {}

    public function index(
        GetAllApplicantTattooRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            ApplicantTattooResource::class
        );

        return response()->json($result);
    }

    public function store(
        StoreApplicantTattooRequest $request
    ): JsonResponse {
        $dto    = ApplicantTattooMapper::fromStoreRequest($request);
        $tattoo = $this->createAction->execute($dto);

        return (new ApplicantTattooResource($tattoo->load('applicant')))
            ->additional(['message' => 'Applicant tattoo created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetApplicantTattooRequest $request,
        ApplicantTattoo $applicantTattoo
    ): JsonResponse {
        return (new ApplicantTattooResource($applicantTattoo->load('applicant')))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateApplicantTattooRequest $request,
        ApplicantTattoo $applicantTattoo
    ): JsonResponse {
        $dto     = ApplicantTattooMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($applicantTattoo, $dto);

        return (new ApplicantTattooResource($updated->load('applicant')))
            ->additional(['message' => 'Applicant tattoo updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteApplicantTattooRequest $request,
        ApplicantTattoo $applicantTattoo
    ): JsonResponse {
        $this->deleteAction->execute($applicantTattoo);

        return response()->json([
            'message' => 'Applicant tattoo deleted successfully.',
        ]);
    }

    public function toggleVisibility(
        ToggleApplicantTattooVisibilityRequest $request,
        ApplicantTattoo $applicantTattoo
    ): JsonResponse {
        $updated = $this->toggleVisibilityAction->execute($applicantTattoo);

        return (new ApplicantTattooResource($updated->load('applicant')))
            ->additional(['message' => 'Applicant tattoo visibility toggled successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get all tattoos for a specific applicant.
     */
    public function listByApplicant(int $applicantId): JsonResponse
    {
        $tattoos = $this->repository->findByApplicantId($applicantId);

        return ApplicantTattooResource::collection($tattoos)
            ->response()
            ->setStatusCode(200);
    }
}