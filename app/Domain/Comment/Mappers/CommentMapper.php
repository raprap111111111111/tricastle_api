<?php

// app/Domain/Comment/Mappers/CommentMapper.php

namespace App\Domain\Comment\Mappers;

use App\Domain\Comment\DTOs\CreateCommentDTO;
use App\Domain\Comment\DTOs\UpdateCommentDTO;
use App\Http\Requests\v1\Comment\StoreCommentRequest;
use App\Http\Requests\v1\Comment\UpdateCommentRequest;

class CommentMapper
{
    public static function fromStoreRequest(StoreCommentRequest $request): CreateCommentDTO
    {
        return new CreateCommentDTO(
            userId:           $request->user()->id,
            commentableType:  $request->validated('commentable_type'),
            commentableId:    $request->validated('commentable_id'),
            content:          $request->validated('content'),
            parentId:         $request->validated('parent_id'),
            isInternal:       $request->validated('is_internal', true),
        );
    }

    public static function fromUpdateRequest(UpdateCommentRequest $request): UpdateCommentDTO
    {
        return new UpdateCommentDTO(
            content:    $request->validated('content'),
            isInternal: $request->validated('is_internal'),
        );
    }
}