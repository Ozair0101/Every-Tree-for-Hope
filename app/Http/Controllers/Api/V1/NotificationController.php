<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Models\PushToken;
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

    /**
     * Register this device to receive pushes.
     *
     * Keyed on the token, not on (user, token): the same handset handed to a
     * colleague produces the same Expo token under a different account, and the
     * row must move to the new owner. Keeping both would send the new user's
     * notifications to a phone the previous one is holding.
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|max:255',
            'platform' => 'nullable|string|max:20',
        ]);

        if (! PushToken::looksValid($validated['token'])) {
            return $this->fail(__('That does not look like an Expo push token.'), null, 422);
        }

        // The model's own helper, so a device retired after failed deliveries is
        // reactivated and its failure count cleared when it comes back.
        PushToken::register($request->user(), $validated['token'], [
            'platform' => $validated['platform'] ?? null,
            'locale' => app()->getLocale(),
        ]);

        return $this->ok(null, __('Device registered for notifications.'));
    }

    /**
     * Stop pushing to this device — called on sign-out.
     *
     * Scoped to the caller so one user cannot unregister another's device by
     * guessing a token.
     */
    public function unregisterDevice(Request $request): JsonResponse
    {
        $validated = $request->validate(['token' => 'required|string|max:255']);

        PushToken::where('user_id', $request->user()->id)
            ->where('token', $validated['token'])
            ->delete();

        return $this->ok(null, __('Device unregistered.'));
    }
}
