<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantLifestyle\Actions\DeleteApplicantLifestyleAction;
use App\Domain\ApplicantLifestyle\Actions\UpsertApplicantLifestyleAction;
use App\Domain\ApplicantLifestyle\Mappers\ApplicantLifestyleMapper;
use App\Domain\ApplicantLifestyle\Repositories\ApplicantLifestyleRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantLifestyle\DeleteApplicantLifestyleRequest;
use App\Http\Requests\v1\ApplicantLifestyle\GetAllApplicantLifestyleRequest;
use App\Http\Requests\v1\ApplicantLifestyle\GetApplicantLifestyleRequest;
use App\Http\Requests\v1\ApplicantLifestyle\UpsertApplicantLifestyleRequest;
use App\Http\Resources\v1\ApplicantLifestyleResource;
use App\Models\ApplicantLifestyle;
use Illuminate\Http\JsonResponse;

class ApplicantLifestyleController extends Controller
{
    public function __construct(
        private readonly ApplicantLifestyleRepository $repository,
        private readonly UpsertApplicantLifestyleAction $upsertAction,
        private readonly DeleteApplicantLifestyleAction $deleteAction,
    ) {}

    public function index(
        GetAllApplicantLifestyleRequest $request
    ): JsonResponse {
        $result = $this->repository->paginate(
            $request->validated(),
            ApplicantLifestyleResource::class
        );

        return response()->json($result);
    }

    public function show(
        GetApplicantLifestyleRequest $request,
        ApplicantLifestyle $applicantLifestyle
    ): JsonResponse {
        return (new ApplicantLifestyleResource($applicantLifestyle->load('applicant')))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Create or update the lifestyle record for a given applicant.
     * (1-to-1 relationship → upsert instead of separate store/update.)
     */
    public function upsert(
        UpsertApplicantLifestyleRequest $request
    ): JsonResponse {
        $dto        = ApplicantLifestyleMapper::fromUpsertRequest($request);
        $lifestyle  = $this->upsertAction->execute($dto);
        $wasCreated = $lifestyle->wasRecentlyCreated;

        return (new ApplicantLifestyleResource($lifestyle->load('applicant')))
            ->additional([
                'message' => $wasCreated
                    ? 'Applicant lifestyle created successfully.'
                    : 'Applicant lifestyle updated successfully.',
            ])
            ->response()
            ->setStatusCode($wasCreated ? 201 : 200);
    }

    public function destroy(
        DeleteApplicantLifestyleRequest $request,
        ApplicantLifestyle $applicantLifestyle
    ): JsonResponse {
        $this->deleteAction->execute($applicantLifestyle);

        return response()->json([
            'message' => 'Applicant lifestyle deleted successfully.',
        ]);
    }

    /**
     * Show the lifestyle record for a specific applicant.
     */
    public function showByApplicant(int $applicantId): JsonResponse
    {
        $lifestyle = $this->repository->findByApplicantId($applicantId);

        if (! $lifestyle) {
            return response()->json([
                'message' => 'No lifestyle record found for this applicant.',
            ], 404);
        }

        return (new ApplicantLifestyleResource($lifestyle->load('applicant')))
            ->response()
            ->setStatusCode(200);
    }
}