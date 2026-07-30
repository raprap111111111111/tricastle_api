<?php

// app/Http/Controllers/v1/OcrTemplateController.php

namespace App\Http\Controllers\v1;

use App\Domain\OcrTemplate\Actions\ApproveOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\CancelOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\CompleteOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\CreateOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\DeleteOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\GetOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\ListOcrTemplatesAction;
use App\Domain\OcrTemplate\Actions\RejectOcrTemplateAction;
use App\Domain\OcrTemplate\Actions\UpdateOcrTemplateAction;
use App\Domain\OcrTemplate\Mappers\OcrTemplateMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OcrTemplate\ApproveOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\CancelOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\CompleteOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\GetAllOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\GetOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\RejectOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\StoreOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\UpdateOcrTemplateRequest;
use App\Http\Resources\v1\OcrTemplateResource;
use App\Models\OcrTemplate;
use Illuminate\Http\JsonResponse;

class OcrTemplateController extends Controller
{
    public function __construct(
        private readonly ListOcrTemplatesAction  $listOcrTemplatesAction,
        private readonly GetOcrTemplateAction    $getOcrTemplateAction,
        private readonly CreateOcrTemplateAction $createOcrTemplateAction,
        private readonly UpdateOcrTemplateAction $updateOcrTemplateAction,
        private readonly DeleteOcrTemplateAction $deleteOcrTemplateAction,
        private readonly ApproveOcrTemplateAction $approveOcrTemplateAction,
        private readonly RejectOcrTemplateAction  $rejectOcrTemplateAction,
        private readonly CompleteOcrTemplateAction $completeOcrTemplateAction,
        private readonly CancelOcrTemplateAction  $cancelOcrTemplateAction,
    ) {}

    public function index(GetAllOcrTemplateRequest $request): JsonResponse
    {
        $result = $this->listOcrTemplatesAction->execute($request->validated());

        return $this->responseSuccess(
            data:    OcrTemplateResource::collection($result),
            message: 'OCR Templates retrieved successfully.',
        );
    }

    public function show(GetOcrTemplateRequest $request, OcrTemplate $ocrTemplate): JsonResponse
    {
        $result = $this->getOcrTemplateAction->execute($ocrTemplate);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template retrieved successfully.',
        );
    }

    public function store(StoreOcrTemplateRequest $request): JsonResponse
    {
        $dto    = OcrTemplateMapper::fromStoreRequest($request);
        $result = $this->createOcrTemplateAction->execute($dto);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template created successfully.',
            code:    201,
        );
    }

    public function update(UpdateOcrTemplateRequest $request, OcrTemplate $ocrTemplate): JsonResponse
    {
        $dto    = OcrTemplateMapper::fromUpdateRequest($request);
        $result = $this->updateOcrTemplateAction->execute($ocrTemplate, $dto);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template updated successfully.',
        );
    }

    public function destroy(OcrTemplate $ocrTemplate): JsonResponse
    {
        $this->deleteOcrTemplateAction->execute($ocrTemplate);

        return $this->responseSuccess(
            message: 'OCR Template deleted successfully.',
        );
    }

    public function approve(ApproveOcrTemplateRequest $request, OcrTemplate $ocrTemplate): JsonResponse
    {
        $dto    = OcrTemplateMapper::fromApproveRequest($request);
        $result = $this->approveOcrTemplateAction->execute($ocrTemplate, $dto);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template approved successfully.',
        );
    }

    public function reject(RejectOcrTemplateRequest $request, OcrTemplate $ocrTemplate): JsonResponse
    {
        $dto    = OcrTemplateMapper::fromRejectRequest($request);
        $result = $this->rejectOcrTemplateAction->execute($ocrTemplate, $dto);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template rejected successfully.',
        );
    }

    public function complete(CompleteOcrTemplateRequest $request, OcrTemplate $ocrTemplate): JsonResponse
    {
        $dto    = OcrTemplateMapper::fromCompleteRequest($request);
        $result = $this->completeOcrTemplateAction->execute($ocrTemplate, $dto);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template completed successfully.',
        );
    }

    public function cancel(CancelOcrTemplateRequest $request, OcrTemplate $ocrTemplate): JsonResponse
    {
        $result = $this->cancelOcrTemplateAction->execute($ocrTemplate);

        return $this->responseSuccess(
            data:    new OcrTemplateResource($result),
            message: 'OCR Template cancelled successfully.',
        );
    }
}