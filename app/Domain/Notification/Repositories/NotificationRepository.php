<?php
// app/Domain/Notification/Repositories/NotificationRepository.php

namespace App\Domain\Notification\Repositories;

use App\Models\Notification;
use App\Support\Query\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class NotificationRepository extends BaseRepository
{
    protected string $model = Notification::class;

    protected array $relations  = [];
    protected array $searchable = ['type'];
    protected array $filterable = ['read_at'];
    protected array $sortable   = ['created_at', 'read_at'];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function query(): Builder
    {
        $query   = parent::query();
        $request = request();

        // Scope to authenticated user's notifications only
        $query->where('notifiable_type', \App\Models\User::class)
              ->where('notifiable_id', auth()->id());

        // Filter: unread only
        if ($request->filled('unread') && $request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        // Filter: read only
        if ($request->filled('read') && $request->boolean('read')) {
            $query->whereNotNull('read_at');
        }

        return $query;
    }

    public function markAsRead(string $id): Notification
    {
        $notification = $this->findOrFail($id);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return $notification->fresh();
    }

    public function markAllAsRead(): int
    {
        return \App\Models\Notification::query()
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function countUnread(): int
    {
        return \App\Models\Notification::query()
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }
}