<?php
// app/Http/Controllers/v1/NotificationController.php

namespace App\Http\Controllers\v1;

use App\Domain\Notification\Actions\DeleteNotificationAction;
use App\Domain\Notification\Actions\GetNotificationAction;
use App\Domain\Notification\Actions\ListNotificationsAction;
use App\Domain\Notification\Actions\MarkAllAsReadAction;
use App\Domain\Notification\Actions\MarkAsReadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Notification\DeleteNotificationRequest;
use App\Http\Requests\v1\Notification\GetAllNotificationRequest;
use App\Http\Requests\v1\Notification\GetNotificationRequest;
use App\Http\Requests\v1\Notification\MarkAllAsReadNotificationRequest;
use App\Http\Requests\v1\Notification\MarkAsReadNotificationRequest;
use App\Http\Resources\v1\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private readonly ListNotificationsAction  $listAction,
        private readonly GetNotificationAction    $getAction,
        private readonly MarkAsReadAction         $markAsReadAction,
        private readonly MarkAllAsReadAction      $markAllAsReadAction,
        private readonly DeleteNotificationAction $deleteAction,
    ) {}

    public function index(GetAllNotificationRequest $request): JsonResponse
    {
        $result = $this->listAction->execute(
            $request->validated(),
            NotificationResource::class
        );

        return $this->responseSuccess($result, 'Notifications retrieved successfully');
    }

    public function show(GetNotificationRequest $request, Notification $notification): JsonResponse
    {
        return $this->responseSuccess(
            new NotificationResource($this->getAction->execute($notification->id)),
            'Notification retrieved successfully'
        );
    }

    public function markAsRead(
        MarkAsReadNotificationRequest $request,
        Notification $notification
    ): JsonResponse {
        return $this->responseSuccess(
            new NotificationResource($this->markAsReadAction->execute($notification)),
            'Notification marked as read'
        );
    }

    public function markAllAsRead(MarkAllAsReadNotificationRequest $request): JsonResponse
    {
        $count = $this->markAllAsReadAction->execute();

        return $this->responseSuccess(
            ['marked_count' => $count],
            "{$count} notifications marked as read"
        );
    }

    public function destroy(
        DeleteNotificationRequest $request,
        Notification $notification
    ): JsonResponse {
        $this->deleteAction->execute($notification);

        return $this->responseSuccess(null, 'Notification deleted successfully');
    }
}