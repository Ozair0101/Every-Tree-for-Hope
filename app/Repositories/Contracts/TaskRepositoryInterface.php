<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Tasks\TaskFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Read-side contract for tasks.
 *
 * The repository exists here because there is something real to hide: filter
 * composition, the sort allow-list, the bounding-box maths and the eager-load
 * strategy are all decisions that should be made once and reused by the API,
 * the admin panel and the scheduler alike. A repository that only forwarded
 * `find()` to Eloquent would be indirection without abstraction — the
 * write-side lives in services instead, which is why there is no `create()`
 * here.
 */
interface TaskRepositoryInterface
{
    /** A filtered, sorted, paginated list with list-shaped eager loading. */
    public function paginate(TaskFilters $filters, int $perPage): LengthAwarePaginator;

    /** One task with everything the detail screen renders. */
    public function findForDetail(string $uuid): ?Task;

    /** Map markers — deliberately lean, and capped. */
    public function markers(TaskFilters $filters, int $limit = 500): Collection;

    /** Counts for the list screen's filter chips, in one query. */
    public function statusCounts(TaskFilters $filters): array;

    /** The caller's own workload summary. */
    public function summaryForUser(User $user): array;
}
