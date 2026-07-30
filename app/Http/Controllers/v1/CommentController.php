<?php

// app/Http/Controllers/v1/CommentController.php

namespace App\Http\Controllers\v1;

use App\Domain\Comment\Actions\CreateCommentAction;
use App\Domain\Comment\Actions\DeleteCommentAction;
use App\Domain\Comment\Actions\GetCommentAction;
use App\Domain\Comment\Actions\ListCommentsAction;
use App\Domain\Comment\Actions\UpdateCommentAction;
use App\Domain\Comment\Mappers\CommentMapper;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Comment\DeleteCommentRequest;
use App\Http\Requests\v1\Comment\GetAllCommentRequest;
use App\Http\Requests\v1\Comment\GetCommentRequest;
use App\Http\Requests\v1\Comment\StoreCommentRequest;
use App\Http\Requests\v1\Comment\UpdateCommentRequest;
use App\Http\Resources\v1\CommentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(
        private readonly ListCommentsAction  $listCommentsAction,
        private readonly GetCommentAction    $getCommentAction,
        private readonly CreateCommentAction $createCommentAction,
        private readonly UpdateCommentAction $updateCommentAction,
        private readonly DeleteCommentAction $deleteCommentAction,
    ) {}

    public function index(GetAllCommentRequest $request): JsonResponse
    {
        $result = $this->listCommentsAction->execute($request->validated());

        return $this->responseSuccess(
            data:    CommentResource::collection($result),
            message: 'Comments retrieved successfully.',
        );
    }

    public function show(GetCommentRequest $request, Comment $comment): JsonResponse
    {
        $result = $this->getCommentAction->execute($comment);

        return $this->responseSuccess(
            data:    new CommentResource($result),
            message: 'Comment retrieved successfully.',
        );
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $dto    = CommentMapper::fromStoreRequest($request);
        $result = $this->createCommentAction->execute($dto);

        return $this->responseSuccess(
            data:    new CommentResource($result),
            message: 'Comment created successfully.',
            code:    201,
        );
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $dto    = CommentMapper::fromUpdateRequest($request);
        $result = $this->updateCommentAction->execute($comment, $dto);

        return $this->responseSuccess(
            data:    new CommentResource($result),
            message: 'Comment updated successfully.',
        );
    }

    public function destroy(DeleteCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->deleteCommentAction->execute($comment);

        return $this->responseSuccess(
            message: 'Comment deleted successfully.',
        );
    }
}