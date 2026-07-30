<?php

namespace App\Http\Controllers\v1;

use App\Domain\OcrFieldExtraction\Actions\AcceptOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Actions\CorrectOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Actions\CreateOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Actions\DeleteOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Actions\GetOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Actions\ListOcrFieldExtractionsAction;
use App\Domain\OcrFieldExtraction\Actions\RejectOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Actions\UpdateOcrFieldExtractionAction;
use App\Domain\OcrFieldExtraction\Mappers\OcrFieldExtractionMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OcrFieldExtraction\AcceptOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\CorrectOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\GetAllOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\RejectOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\StoreOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\UpdateOcrFieldExtractionRequest;
use App\Http\Resources\v1\OcrFieldExtractionResource;
use App\Models\OcrFieldExtraction;
use Illuminate\Http\JsonResponse;

class OcrFieldExtractionController extends Controller
{
    public function __construct(
        private readonly ListOcrFieldExtractionsAction  $listAction,
        private readonly GetOcrFieldExtractionAction    $getAction,
        private readonly CreateOcrFieldExtractionAction $createAction,
        private readonly UpdateOcrFieldExtractionAction $updateAction,
        private readonly DeleteOcrFieldExtractionAction $deleteAction,
        private readonly CorrectOcrFieldExtractionAction $correctAction,
        private readonly AcceptOcrFieldExtractionAction  $acceptAction,
        private readonly RejectOcrFieldExtractionAction  $rejectAction,
    ) {}

    public function index(GetAllOcrFieldExtractionRequest $request): JsonResponse
    {
        $result = $this->listAction->execute($request->validated(), OcrFieldExtractionResource::class);
        return $this->responseSuccess($result, 'OCR field extractions retrieved successfully');
    }

    public function show(OcrFieldExtraction $ocrFieldExtraction): JsonResponse
    {
        return $this->responseSuccess(
            new OcrFieldExtractionResource($this->getAction->execute($ocrFieldExtraction->id)),
            'OCR field extraction retrieved successfully'
        );
    }

    public function store(StoreOcrFieldExtractionRequest $request): JsonResponse
    {
        $result = $this->createAction->execute(
            OcrFieldExtractionMapper::fromCreateRequest($request)
        );
        return $this->responseSuccess(
            new OcrFieldExtractionResource($result),
            'OCR field extraction created successfully',
            201
        );
    }

    public function update(UpdateOcrFieldExtractionRequest $request, OcrFieldExtraction $ocrFieldExtraction): JsonResponse
    {
        $result = $this->updateAction->execute(
            $ocrFieldExtraction,
            OcrFieldExtractionMapper::fromUpdateRequest($request)
        );
        return $this->responseSuccess(
            new OcrFieldExtractionResource($result),
            'OCR field extraction updated successfully'
        );
    }

    public function destroy(OcrFieldExtraction $ocrFieldExtraction): JsonResponse
    {
        $this->deleteAction->execute($ocrFieldExtraction);
        return $this->responseSuccess(null, 'OCR field extraction deleted successfully');
    }

    public function correct(CorrectOcrFieldExtractionRequest $request, OcrFieldExtraction $ocrFieldExtraction): JsonResponse
    {
        $result = $this->correctAction->execute(
            $ocrFieldExtraction,
            OcrFieldExtractionMapper::fromCorrectRequest($request)
        );
        return $this->responseSuccess(
            new OcrFieldExtractionResource($result),
            'OCR field extraction corrected successfully'
        );
    }

    public function accept(AcceptOcrFieldExtractionRequest $request, OcrFieldExtraction $ocrFieldExtraction): JsonResponse
    {
        $result = $this->acceptAction->execute(
            $ocrFieldExtraction,
            OcrFieldExtractionMapper::fromAcceptRequest($request)
        );
        return $this->responseSuccess(
            new OcrFieldExtractionResource($result),
            'OCR field extraction accepted successfully'
        );
    }

    public function reject(RejectOcrFieldExtractionRequest $request, OcrFieldExtraction $ocrFieldExtraction): JsonResponse
    {
        $result = $this->rejectAction->execute(
            $ocrFieldExtraction,
            OcrFieldExtractionMapper::fromRejectRequest($request)
        );
        return $this->responseSuccess(
            new OcrFieldExtractionResource($result),
            'OCR field extraction rejected successfully'
        );
    }
}