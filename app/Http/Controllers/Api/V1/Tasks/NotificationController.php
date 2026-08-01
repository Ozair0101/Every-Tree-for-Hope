<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\Tasks\TaskNotificationResource;
use App\Models\NotificationPreference;
use App\Repositories\Contracts\TaskNotificationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The task module's in-app inbox.
 *
 *   GET    /tasks/notifications               — the inbox (paginated)
 *   GET    /tasks/notifications/unread-count  — the badge
 *   POST   /tasks/notifications/read-all      — clear the badge
 *   POST   /tasks/notifications/{id}/read     — mark one read
 *   POST   /tasks/notifications/{id}/unread   — put one back
 *   DELETE /tasks/notifications/{id}          — remove one
 *   GET    /tasks/notifications/preferences   — current opt-outs
 *   PUT    /tasks/notifications/preferences   — change them
 *
 * Deliberately NOT merged into the existing `Api\V1\NotificationController`,
 * which serves Laravel's `notifications` table for tree and voice moderation
 * and is already shipped. Two inboxes with different shapes are less confusing
 * than one endpoint that returns two shapes.
 *
 * Every read is scoped to the caller inside the repository query rather than
 * checked afterwards — an inbox is the easiest place in an API to leak another
 * user's data, and a forgotten ownership check is a silent breach rather than
 * a visible error.
 */
class NotificationController extends ApiController
{
    public function __construct(
        private readonly TaskNotificationRepositoryInterface $notifications,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unread' => ['sometimes', 'boolean'],
            'type' => ['sometimes', 'string', 'max:60'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $notifications = $this->notifications->paginateForUser(
            user: $request->user(),
            unreadOnly: isset($validated['unread']) ? $request->boolean('unread') : null,
            type: $validated['type'] ?? null,
            perPage: $this->perPage($request, 20),
        );

        return $this->paginated($notifications, TaskNotificationResource::class, [
            'unread' => $this->notifications->unreadCount($request->user()),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->ok(['count' => $this->notifications->unreadCount($request->user())]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = $this->notifications->findForUser($request->user(), $id);

        if ($notification === null) {
            // 404 rather than 403: confirming that someone else's notification
            // exists is itself a small leak.
            return $this->fail('Notification not found.', status: 404);
        }

        $notification->markAsRead();

        return $this->ok([
            'unread' => $this->notifications->unreadCount($request->user()),
        ], 'Marked as read.');
    }

    public function markUnread(Request $request, int $id): JsonResponse
    {
        $notification = $this->notifications->findForUser($request->user(), $id);

        if ($notification === null) {
            return $this->fail('Notification not found.', status: 404);
        }

        $notification->markAsUnread();

        return $this->ok([
            'unread' => $this->notifications->unreadCount($request->user()),
        ], 'Marked as unread.');
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $count = $this->notifications->markAllRead($request->user());

        return $this->ok(['marked' => $count, 'unread' => 0], 'All notifications marked as read.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $notification = $this->notifications->findForUser($request->user(), $id);

        if ($notification === null) {
            return $this->fail('Notification not found.', status: 404);
        }

        $notification->delete();

        return $this->ok([
            'unread' => $this->notifications->unreadCount($request->user()),
        ], 'Notification removed.');
    }

    /**
     * Which task events this user still wants.
     *
     * Absence of a row means enabled, so the response is composed from the full
     * event list rather than from the table — otherwise a user who has never
     * changed anything would get an empty settings screen.
     */
    public function preferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $disabled = NotificationPreference::query()
            ->where('user_id', $user->id)
            ->where('channel', NotificationPreference::CHANNEL_PUSH)
            ->disabled()
            ->pluck('event_key')
            ->flip();

        $preferences = collect(NotificationPreference::eventKeys())
            ->map(fn (string $key) => [
                'event_key' => $key,
                'enabled' => ! $disabled->has($key),
            ])
            ->values();

        return $this->ok(['channel' => 'push', 'preferences' => $preferences]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'preferences' => ['required', 'array', 'min:1'],
            'preferences.*.event_key' => ['required', 'string', Rule::in(NotificationPreference::eventKeys())],
            'preferences.*.enabled' => ['required', 'boolean'],
        ]);

        $user = $request->user();

        foreach ($validated['preferences'] as $preference) {
            if ($preference['enabled']) {
                NotificationPreference::optIn($user->id, NotificationPreference::CHANNEL_PUSH, $preference['event_key']);
            } else {
                NotificationPreference::optOut($user->id, NotificationPreference::CHANNEL_PUSH, $preference['event_key']);
            }
        }

        return $this->preferences($request);
    }
}
