<?php

namespace App\Repositories\Tasks;

use App\Models\TaskNotification;
use App\Models\User;
use App\Repositories\Contracts\TaskNotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTaskNotificationRepository implements TaskNotificationRepositoryInterface
{
    public function paginateForUser(User $user, ?bool $unreadOnly, ?string $type, int $perPage): LengthAwarePaginator
    {
        return TaskNotification::query()
            ->forUser($user->id)
            ->when($unreadOnly === true, fn ($q) => $q->unread())
            ->when($type, fn ($q, $t) => $q->where('type', $t))
            // Only what the row renders. The task is loaded for its title and
            // reference so the inbox can deep-link without a second round trip.
            ->with('task:id,uuid,reference,title,status')
            ->latestFirst()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function unreadCount(User $user): int
    {
        // Hits (user_id, is_read) directly — this runs on every screen the app
        // renders, so it must never become a scan.
        return TaskNotification::query()->forUser($user->id)->unread()->count();
    }

    /**
     * Scoped to the owner by the query, not by a check afterwards.
     *
     * An inbox is the easiest place in an API to leak another user's data: a
     * plain `find($id)` followed by a forgotten ownership check reads a
     * stranger's notification. Putting the constraint in the lookup makes the
     * mistake impossible rather than merely unlikely.
     */
    public function findForUser(User $user, int $id): ?TaskNotification
    {
        return TaskNotification::query()
            ->forUser($user->id)
            ->whereKey($id)
            ->first();
    }

    public function markAllRead(User $user): int
    {
        // A mass update, not a loop: a volunteer clearing a month of badges
        // should be one statement. `read_at` is set here rather than relying on
        // the model's saving hook, which a mass update bypasses.
        return TaskNotification::query()
            ->forUser($user->id)
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}
