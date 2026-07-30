<?php

// app/Domain/Comment/Repositories/CommentRepository.php

namespace App\Domain\Comment\Repositories;

use App\Models\Comment;
use App\Support\Query\BaseRepository;

class CommentRepository extends BaseRepository
{
    protected string $model = Comment::class;

    protected array $relations = [
        'user',
        'parent',
        'replies',
        'commentable',
    ];

    protected array $searchable = [
        'content',
    ];

    protected array $filterable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'is_internal',
    ];

    protected array $sortable = [
        'id',
        'user_id',
        'commentable_type',
        'commentable_id',
        'is_internal',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    
}