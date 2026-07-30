<?php

namespace App\Http\Controllers\v1;

use App\Domain\FileRepository\Actions\DeleteFileRepositoryAction;
use App\Domain\FileRepository\Actions\GetFileRepositoryAction;
use App\Domain\FileRepository\Actions\ListFileRepositoriesAction;
use App\Domain\FileRepository\Actions\PurgeFileRepositoryAction;
use App\Domain\FileRepository\Actions\UploadFileRepositoryAction;
use App\Domain\FileRepository\Mappers\FileRepositoryMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\FileRepository\DeleteFileRepositoryRequest;
use App\Http\Requests\v1\FileRepository\GetAllFileRepositoryRequest;
use App\Http\Requests\v1\FileRepository\GetFileRepositoryRequest;
use App\Http\Requests\v1\FileRepository\PurgeFileRepositoryRequest;
use App\Http\Requests\v1\FileRepository\UploadFileRepositoryRequest;
use App\Http\Resources\v1\FileRepositoryResource;
use App\Models\FileRepository;
use Illuminate\Http\JsonResponse;

class FileRepositoryController extends Controller
{
    public function __construct(
        private readonly ListFileRepositoriesAction $listAction,
        private readonly GetFileRepositoryAction    $getAction,
        private readonly UploadFileRepositoryAction $uploadAction,
        private readonly DeleteFileRepositoryAction $deleteAction,
        private readonly PurgeFileRepositoryAction  $purgeAction,
    ) {}

    public function index(GetAllFileRepositoryRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            FileRepositoryResource::class
        );

        return $this->responseSuccess($result, 'File repository retrieved successfully');
    }

    public function show(GetFileRepositoryRequest $request, FileRepository $fileRepository): JsonResponse
    {
        $result = $this->getAction->execute($fileRepository->id);

        return $this->responseSuccess(
            new FileRepositoryResource($result),
            'File retrieved successfully'
        );
    }

    public function store(UploadFileRepositoryRequest $request): JsonResponse
    {
        $file = $this->uploadAction->execute(
            FileRepositoryMapper::fromUploadRequest($request)
        );

        return $this->responseSuccess(
            new FileRepositoryResource($file),
            'File uploaded successfully',
            JsonResponse::HTTP_CREATED
        );
    }

    public function destroy(DeleteFileRepositoryRequest $request, FileRepository $fileRepository): JsonResponse
    {
        $this->deleteAction->execute($fileRepository);

        return $this->responseSuccess(
            null,
            'File deleted successfully'
        );
    }

    public function purge(PurgeFileRepositoryRequest $request, FileRepository $fileRepository): JsonResponse
    {
        $this->purgeAction->execute($fileRepository);

        return $this->responseSuccess(
            null,
            'File permanently purged successfully'
        );
    }
}