<?php

namespace App\Domain\OcrJob\Mappers;

use App\Domain\OcrJob\DTOs\CancelOcrJobDTO;
use App\Domain\OcrJob\DTOs\CreateOcrJobDTO;
use App\Domain\OcrJob\DTOs\QueueOcrJobDTO;
use App\Domain\OcrJob\DTOs\RetryOcrJobDTO;
use App\Domain\OcrJob\DTOs\ReviewOcrJobDTO;
use App\Domain\OcrJob\DTOs\UpdateOcrJobDTO;
use App\Http\Requests\v1\OcrJob\CancelOcrJobRequest;
use App\Http\Requests\v1\OcrJob\QueueOcrJobRequest;
use App\Http\Requests\v1\OcrJob\RetryOcrJobRequest;
use App\Http\Requests\v1\OcrJob\ReviewOcrJobRequest;
use App\Http\Requests\v1\OcrJob\StoreOcrJobRequest;
use App\Http\Requests\v1\OcrJob\UpdateOcrJobRequest;

class OcrJobMapper
{
    public static function fromCreateRequest(StoreOcrJobRequest $request): CreateOcrJobDTO
    {
        return new CreateOcrJobDTO(
            applicantDocumentId: $request->validated('applicant_document_id'),
            fileRepositoryId:    $request->validated('file_repository_id'),
            ocrTemplateId:       $request->validated('ocr_template_id'),
            batchId:             $request->validated('batch_id'),
            provider:            $request->validated('provider', 'tesseract'),
            providerConfig:      $request->validated('provider_config'),
            priority:            $request->validated('priority', 5),
            notes:               $request->validated('notes'),
            metadata:            $request->validated('metadata'),
        );
    }

    public static function fromUpdateRequest(UpdateOcrJobRequest $request): UpdateOcrJobDTO
    {
        return new UpdateOcrJobDTO(
            statusMessage:  $request->validated('status_message'),
            provider:       $request->validated('provider'),
            providerConfig: $request->validated('provider_config'),
            priority:       $request->validated('priority'),
            notes:          $request->validated('notes'),
            metadata:       $request->validated('metadata'),
            maxAttempts:    $request->validated('max_attempts'),
        );
    }

    public static function fromQueueRequest(QueueOcrJobRequest $request, int $ocrJobId): QueueOcrJobDTO
    {
        return new QueueOcrJobDTO(
            ocrJobId: $ocrJobId,
            priority: $request->validated('priority', 5),
        );
    }

    public static function fromCancelRequest(CancelOcrJobRequest $request): CancelOcrJobDTO
    {
        return new CancelOcrJobDTO(
            notes: $request->validated('notes'),
        );
    }

    public static function fromRetryRequest(RetryOcrJobRequest $request): RetryOcrJobDTO
    {
        return new RetryOcrJobDTO(
            provider: $request->validated('provider'),
            priority: $request->validated('priority'),
            notes:    $request->validated('notes'),
        );
    }

    public static function fromReviewRequest(ReviewOcrJobRequest $request): ReviewOcrJobDTO
    {
        return new ReviewOcrJobDTO(
            status: $request->validated('status'),
            notes:  $request->validated('notes'),
        );
    }
}