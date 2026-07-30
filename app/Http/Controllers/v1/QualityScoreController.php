<?php

namespace App\Http\Controllers\v1;

use App\Domain\QualityScore\Actions\CreateQualityScoreAction;
use App\Domain\QualityScore\Actions\DeleteQualityScoreAction;
use App\Domain\QualityScore\Actions\GetQualityScoreAction;
use App\Domain\QualityScore\Actions\ListQualityScoresAction;
use App\Domain\QualityScore\Actions\RecalculateQualityScoreAction;
use App\Domain\QualityScore\Actions\UpdateQualityScoreAction;
use App\Domain\QualityScore\Mappers\QualityScoreMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\QualityScore\DeleteQualityScoreRequest;
use App\Http\Requests\v1\QualityScore\GetAllQualityScoreRequest;
use App\Http\Requests\v1\QualityScore\GetQualityScoreRequest;
use App\Http\Requests\v1\QualityScore\RecalculateQualityScoreRequest;
use App\Http\Requests\v1\QualityScore\StoreQualityScoreRequest;
use App\Http\Requests\v1\QualityScore\UpdateQualityScoreRequest;
use App\Http\Resources\v1\QualityScoreResource;
use App\Models\Applicant;
use App\Models\QualityScore;
use Illuminate\Http\JsonResponse;

class QualityScoreController extends Controller
{
    public function __construct(
        private readonly ListQualityScoresAction       $listAction,
        private readonly GetQualityScoreAction         $getAction,
        private readonly CreateQualityScoreAction      $createAction,
        private readonly UpdateQualityScoreAction      $updateAction,
        private readonly DeleteQualityScoreAction      $deleteAction,
        private readonly RecalculateQualityScoreAction $recalculateAction,
    ) {}

    public function index(GetAllQualityScoreRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            QualityScoreResource::class
        );

        return $this->responseSuccess($result, 'Quality scores retrieved successfully');
    }

    public function show(GetQualityScoreRequest $request, QualityScore $qualityScore): JsonResponse
    {
        $result = $this->getAction->execute($qualityScore->id);

        return $this->responseSuccess(
            new QualityScoreResource($result),
            'Quality score retrieved successfully'
        );
    }

    public function store(StoreQualityScoreRequest $request): JsonResponse
    {
        $qualityScore = $this->createAction->execute(
            QualityScoreMapper::fromCreateRequest($request)
        );

        return $this->responseSuccess(
            new QualityScoreResource($qualityScore),
            'Quality score created successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(UpdateQualityScoreRequest $request, QualityScore $qualityScore): JsonResponse
    {
        $updated = $this->updateAction->execute(
            $qualityScore,
            QualityScoreMapper::fromUpdateRequest($request)
        );

        return $this->responseSuccess(
            new QualityScoreResource($updated),
            'Quality score updated successfully'
        );
    }

    public function destroy(DeleteQualityScoreRequest $request, QualityScore $qualityScore): JsonResponse
    {
        $this->deleteAction->execute($qualityScore);

        return $this->responseSuccess(null, 'Quality score deleted successfully');
    }

    public function recalculate(RecalculateQualityScoreRequest $request): JsonResponse
    {
        $applicant = Applicant::findOrFail($request->validated('applicant_id'));

        $qualityScore = $this->recalculateAction->execute(
            $applicant,
            $request->user()->id
        );

        return $this->responseSuccess(
            new QualityScoreResource($qualityScore),
            'Quality score recalculated successfully'
        );
    }
}