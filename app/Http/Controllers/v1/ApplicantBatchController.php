<?php

namespace App\Http\Controllers\v1;

use App\Domain\ApplicantBatch\Actions\AcceptApplicantBatchAction;
use App\Domain\ApplicantBatch\Actions\CreateApplicantBatchAction;
use App\Domain\ApplicantBatch\Actions\DeleteApplicantBatchAction;
use App\Domain\ApplicantBatch\Actions\DeployApplicantBatchAction;
use App\Domain\ApplicantBatch\Actions\RecordExamResultAction;
use App\Domain\ApplicantBatch\Actions\RejectApplicantBatchAction;
use App\Domain\ApplicantBatch\Actions\ScheduleInterviewAction;
use App\Domain\ApplicantBatch\Actions\UpdateApplicantBatchAction;
use App\Domain\ApplicantBatch\Actions\UpdateApplicantBatchStatusAction;
use App\Domain\ApplicantBatch\Actions\WithdrawApplicantBatchAction;
use App\Domain\ApplicantBatch\Mappers\ApplicantBatchMapper;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ApplicantBatch\AcceptApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\DeleteApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\DeployApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\GetAllApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\GetApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\RecordExamResultRequest;
use App\Http\Requests\v1\ApplicantBatch\RejectApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\ScheduleInterviewRequest;
use App\Http\Requests\v1\ApplicantBatch\StoreApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\UpdateApplicantBatchRequest;
use App\Http\Requests\v1\ApplicantBatch\UpdateApplicantBatchStatusRequest;
use App\Http\Requests\v1\ApplicantBatch\WithdrawApplicantBatchRequest;
use App\Http\Resources\v1\ApplicantBatchResource;
use App\Models\ApplicantBatch;
use Illuminate\Http\JsonResponse;

class ApplicantBatchController extends Controller
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository,
        private readonly CreateApplicantBatchAction $createAction,
        private readonly UpdateApplicantBatchAction $updateAction,
        private readonly DeleteApplicantBatchAction $deleteAction,
        private readonly UpdateApplicantBatchStatusAction $updateStatusAction,
        private readonly ScheduleInterviewAction $scheduleInterviewAction,
        private readonly RecordExamResultAction $recordExamResultAction,
        private readonly AcceptApplicantBatchAction $acceptAction,
        private readonly RejectApplicantBatchAction $rejectAction,
        private readonly WithdrawApplicantBatchAction $withdrawAction,
        private readonly DeployApplicantBatchAction $deployAction,
    ) {}

    public function index(GetAllApplicantBatchRequest $request): JsonResponse
    {
        $result = $this->repository->paginate(
            $request->validated(),
            ApplicantBatchResource::class
        );

        return response()->json($result);
    }

    public function store(StoreApplicantBatchRequest $request): JsonResponse
    {
        $dto      = ApplicantBatchMapper::fromStoreRequest($request);
        $applied  = $this->createAction->execute($dto);

        return (new ApplicantBatchResource($applied->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Applicant applied to batch successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        GetApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        return (new ApplicantBatchResource($applicantBatch->load(['applicant', 'batch', 'processedBy'])))
            ->response()
            ->setStatusCode(200);
    }

    public function update(
        UpdateApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $dto     = ApplicantBatchMapper::fromUpdateRequest($request);
        $updated = $this->updateAction->execute($applicantBatch, $dto);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Applicant batch updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(
        DeleteApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $this->deleteAction->execute($applicantBatch);

        return response()->json([
            'message' => 'Applicant batch deleted successfully.',
        ]);
    }

    public function updateStatus(
        UpdateApplicantBatchStatusRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $dto     = ApplicantBatchMapper::fromUpdateStatusRequest($request);
        $updated = $this->updateStatusAction->execute($applicantBatch, $dto);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Status updated successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function scheduleInterview(
        ScheduleInterviewRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $dto     = ApplicantBatchMapper::fromScheduleInterviewRequest($request);
        $updated = $this->scheduleInterviewAction->execute($applicantBatch, $dto);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Interview scheduled successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function recordExamResult(
        RecordExamResultRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $dto     = ApplicantBatchMapper::fromRecordExamResultRequest($request);
        $updated = $this->recordExamResultAction->execute($applicantBatch, $dto);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Exam result recorded successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function accept(
        AcceptApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $updated = $this->acceptAction->execute($applicantBatch, $request->user()?->id);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Applicant accepted successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function reject(
        RejectApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $dto     = ApplicantBatchMapper::fromRejectRequest($request);
        $updated = $this->rejectAction->execute($applicantBatch, $dto);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Applicant rejected successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function withdraw(
        WithdrawApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $updated = $this->withdrawAction->execute($applicantBatch, $request->user()?->id);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Application withdrawn successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function deploy(
        DeployApplicantBatchRequest $request,
        ApplicantBatch $applicantBatch
    ): JsonResponse {
        $updated = $this->deployAction->execute($applicantBatch, $request->user()?->id);

        return (new ApplicantBatchResource($updated->load(['applicant', 'batch', 'processedBy'])))
            ->additional(['message' => 'Applicant deployed successfully.'])
            ->response()
            ->setStatusCode(200);
    }

    public function listByApplicant(int $applicantId): JsonResponse
    {
        $records = $this->repository->findByApplicantId($applicantId);

        return ApplicantBatchResource::collection($records)
            ->response()
            ->setStatusCode(200);
    }

    public function listByBatch(int $batchId): JsonResponse
    {
        $records = $this->repository->findByBatchId($batchId);

        return ApplicantBatchResource::collection($records)
            ->response()
            ->setStatusCode(200);
    }
}