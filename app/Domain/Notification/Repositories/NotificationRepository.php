<?php

namespace App\Domain\Notification\Repositories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationRepository
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $userId = $filters['user_id'] ?? Auth::id();

        // 🔍 DEBUG LOGS
        Log::info('🔔 NotificationRepository::list', [
            'user_id'          => $userId,
            'auth_user'        => Auth::id(),
            'user_model_class' => User::class,
            'filters'          => $filters,
        ]);

        // Count ALL notifications in DB (any user)
        $totalInDb = Notification::count();
        Log::info("📊 Total notifications in DB: {$totalInDb}");

        // Count for THIS user with User::class
        $forThisUser = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->count();
        Log::info("📊 Notifications for user {$userId} (User::class): {$forThisUser}");

        // Check what notifiable_type values exist
        $types = Notification::distinct()->pluck('notifiable_type')->toArray();
        Log::info('📊 Distinct notifiable_type values in DB:', $types);

        // Check what notifiable_ids exist
        $ids = Notification::distinct()->pluck('notifiable_id')->toArray();
        Log::info('📊 Distinct notifiable_ids in DB:', $ids);

        // Build query
        $query = Notification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id',   $userId);

        // Filter by read/unread
        if (isset($filters['is_read']) && $filters['is_read'] !== null && $filters['is_read'] !== '') {
            $isRead = filter_var($filters['is_read'], FILTER_VALIDATE_BOOLEAN);
            $isRead
                ? $query->whereNotNull('read_at')
                : $query->whereNull('read_at');
        }

        // Filter by module
        if (!empty($filters['module'])) {
            $query->where('data', 'like', '%"module":"' . $filters['module'] . '"%');
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where('data', 'like', '%' . $filters['search'] . '%');
        }

        // Sort
        $orderBy  = $filters['order_by']  ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        $result = $query->paginate($filters['limit'] ?? 10);

        Log::info("✅ Final query result: {$result->total()} notifications returned");

        return $result;
    }

    public function find(string $id): ?Notification
    {
        return Notification::where('id', $id)
            ->where('notifiable_id', Auth::id())
            ->first();
    }

    public function markAsRead(Notification $notification): Notification
    {
        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }
        return $notification->fresh();
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }
}