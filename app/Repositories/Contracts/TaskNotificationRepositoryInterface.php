<?php

namespace App\Repositories\Contracts;

use App\Models\TaskNotification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Read-side contract for the task inbox.
 *
 * Small on purpose — an inbox has few questions to answer, but they are asked
 * constantly (the badge count runs on every screen), so having one place that
 * owns those queries keeps the indexes and the shapes aligned.
 */
interface TaskNotificationRepositoryInterface
{
    public function paginateForUser(User $user, ?bool $unreadOnly, ?string $type, int $perPage): LengthAwarePaginator;

    public function unreadCount(User $user): int;

    /** Null when the notification is not this user's — never another user's row. */
    public function findForUser(User $user, int $id): ?TaskNotification;

    /** @return int how many were marked */
    public function markAllRead(User $user): int;
}
