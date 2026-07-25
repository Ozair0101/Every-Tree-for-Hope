<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * In-app notification centre for the mobile client.
 *
 * Reads the signed-in user's database notifications — tree submissions for
 * moderators, review outcomes for planters — and lets the app mark them read.
 *
 *   GET  /notifications              — the user's notifications (paginated)
 *   GET  /notifications/unread-count — badge count
 *   POST /notifications/read-all     — mark every one read
 *   POST /notifications/{id}/read    — mark one read
 */
class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate($this->perPage($request, 20));

        return $this->paginated($notifications, NotificationResource::class, [
            'unread' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->ok(['count' => $request->user()->unreadNotifications()->count()]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->whereKey($id)->first();

        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }

        return $this->ok(['unread' => $request->user()->unreadNotifications()->count()]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->ok(['unread' => 0]);
    }
}
