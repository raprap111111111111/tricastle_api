<?php

namespace App\Http\Controllers\v1;

use App\Domain\Deployment\Actions\BulkDeployAction;
use App\Domain\Deployment\Actions\CancelDeploymentAction;
use App\Domain\Deployment\Actions\DeployApplicantAction;
use App\Domain\Deployment\Actions\GetDeploymentAction;
use App\Domain\Deployment\Actions\GetDeploymentStatsAction;
use App\Domain\Deployment\Actions\ListDeploymentsAction;
use App\Domain\Deployment\Actions\UpdateDeploymentAction;
use App\Domain\Deployment\Mappers\DeploymentMapper;
use App\Domain\Deployment\Repositories\DeploymentRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Deployment\BulkDeployRequest;
use App\Http\Requests\v1\Deployment\CancelDeploymentRequest;
use App\Http\Requests\v1\Deployment\DeployApplicantRequest;
use App\Http\Requests\v1\Deployment\GetAllDeploymentRequest;
use App\Http\Requests\v1\Deployment\UpdateDeploymentRequest;
use App\Http\Resources\v1\DeploymentResource;
use App\Models\ApplicantBatch;
use Illuminate\Http\JsonResponse;

class DeploymentController extends Controller
{
    public function __construct(
        private readonly ListDeploymentsAction     $listAction,
        private readonly GetDeploymentAction       $getAction,
        private readonly DeployApplicantAction     $deployAction,
        private readonly UpdateDeploymentAction    $updateAction,
        private readonly CancelDeploymentAction    $cancelAction,
        private readonly BulkDeployAction          $bulkAction,
        private readonly GetDeploymentStatsAction  $statsAction,
        private readonly DeploymentRepository      $repository,
    ) {}

    /**
     * List deployments with filters.
     * GET /api/v1/deployments
     */
    public function index(GetAllDeploymentRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            DeploymentResource::class
        );

        return $this->responseSuccess($result, 'Deployments retrieved successfully');
    }

    /**
     * Get a single deployment.
     * GET /api/v1/deployments/{id}
     */
    public function show(ApplicantBatch $deployment): JsonResponse
    {
        $deployment->load([
            'applicant',
            'batch',
            'processedBy',
            'cancelledBy',
        ]);

        return $this->responseSuccess(
            new DeploymentResource($deployment),
            'Deployment retrieved successfully'
        );
    }

    /**
     * Deploy an applicant (change status from any → deployed).
     * PATCH /api/v1/deployments/{applicant_batch}/deploy
     */
    public function deploy(
        DeployApplicantRequest $request,
        ApplicantBatch $deployment,
    ): JsonResponse {
        $updated = $this->deployAction->execute(
            $deployment,
            DeploymentMapper::fromDeployRequest($request),
        );

        return $this->responseSuccess(
            new DeploymentResource($updated),
            'Applicant deployed successfully'
        );
    }

    /**
     * Update a deployment's info (does not change status).
     * PUT /api/v1/deployments/{id}
     */
    public function update(
        UpdateDeploymentRequest $request,
        ApplicantBatch $deployment,
    ): JsonResponse {
        $updated = $this->updateAction->execute(
            $deployment,
            DeploymentMapper::fromUpdateRequest($request),
        );

        return $this->responseSuccess(
            new DeploymentResource($updated),
            'Deployment updated successfully'
        );
    }

    /**
     * Cancel a deployment.
     * PATCH /api/v1/deployments/{id}/cancel
     */
    public function cancel(
        CancelDeploymentRequest $request,
        ApplicantBatch $deployment,
    ): JsonResponse {
        $updated = $this->cancelAction->execute(
            $deployment,
            DeploymentMapper::fromCancelRequest($request),
        );

        return $this->responseSuccess(
            new DeploymentResource($updated),
            'Deployment cancelled successfully'
        );
    }

    /**
     * Bulk deploy multiple applicants.
     * POST /api/v1/deployments/bulk
     */
    public function bulk(BulkDeployRequest $request): JsonResponse
    {
        $result = $this->bulkAction->execute(
            $request->validated('applicant_batch_ids'),
            DeploymentMapper::fromBulkDeployRequest($request),
        );

        $message = sprintf(
            'Deployed %d of %d applicants successfully',
            $result['success_count'],
            $result['total'],
        );

        return $this->responseSuccess($result, $message);
    }

    /**
     * Get deployment statistics.
     * GET /api/v1/deployments/stats
     */
    public function stats(): JsonResponse
    {
        return $this->responseSuccess(
            $this->statsAction->execute(),
            'Deployment stats retrieved successfully'
        );
    }

    /**
     * Get distinct deployment countries (for filter dropdown).
     * GET /api/v1/deployments/countries
     */
    public function countries(): JsonResponse
    {
        return $this->responseSuccess(
            $this->repository->distinctCountries(),
            'Countries retrieved successfully'
        );
    }
}