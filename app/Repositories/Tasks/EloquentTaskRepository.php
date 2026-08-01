<?php

namespace App\Repositories\Tasks;

use App\Enums\TaskAssignmentRole;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function paginate(TaskFilters $filters, int $perPage): LengthAwarePaginator
    {
        return $this->applySort($this->query($filters), $filters)
            ->forList()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findForDetail(string $uuid): ?Task
    {
        return Task::query()->forDetail()->byUuid($uuid)->first();
    }

    public function markers(TaskFilters $filters, int $limit = 500): Collection
    {
        return $this->query($filters)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(['id', 'uuid', 'reference', 'title', 'status', 'priority', 'latitude', 'longitude', 'radius', 'due_date'])
            ->limit($limit)
            ->get();
    }

    /**
     * Counts per status for the filter chips.
     *
     * One grouped query, not one per chip. Deliberately ignores the status
     * filter itself — a chip has to show its own count even while a different
     * chip is active, otherwise every unselected chip reads zero.
     */
    public function statusCounts(TaskFilters $filters): array
    {
        $rows = $this->query($filters, ignoreStatus: true)
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $counts = ['all' => (int) $rows->sum()];

        foreach (TaskStatus::cases() as $status) {
            $counts[$status->value] = (int) ($rows[$status->value] ?? 0);
        }

        return $counts;
    }

    public function summaryForUser(User $user): array
    {
        // One pass over the user's assignments rather than five COUNT queries.
        //
        // `now()` is bound rather than written as NOW(): that function does not
        // exist in SQLite, and a summary that only works on one engine is a
        // summary nobody can test. Everything else here is standard SQL.
        $row = Task::query()
            ->whereHas('assignments', fn (Builder $q) => $q
                ->where('user_id', $user->id)
                ->where('role', TaskAssignmentRole::ASSIGNEE->value))
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status IN (?, ?, ?) THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as awaiting_review,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN due_date IS NOT NULL AND due_date < ? AND status NOT IN (?, ?, ?) THEN 1 ELSE 0 END) as overdue
            ', [
                TaskStatus::ASSIGNED->value, TaskStatus::IN_PROGRESS->value, TaskStatus::REJECTED->value,
                TaskStatus::SUBMITTED->value, TaskStatus::UNDER_REVIEW->value,
                TaskStatus::APPROVED->value,
                now(),
                TaskStatus::DRAFT->value, TaskStatus::APPROVED->value, TaskStatus::CANCELLED->value,
            ])
            ->first();

        return [
            'total' => (int) $row->total,
            'open' => (int) $row->open,
            'awaiting_review' => (int) $row->awaiting_review,
            'approved' => (int) $row->approved,
            'overdue' => (int) $row->overdue,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Filter composition
    |--------------------------------------------------------------------------
    */

    private function query(TaskFilters $filters, bool $ignoreStatus = false): Builder
    {
        $query = Task::query();

        if (! $ignoreStatus && $statuses = $filters->validStatuses()) {
            $query->whereIn('status', $statuses);
        }

        if ($priorities = $filters->validPriorities()) {
            $query->whereIn('priority', $priorities);
        }

        $query
            ->when($filters->categoryId, fn (Builder $q, $id) => $q->where('task_category_id', $id))
            ->when($filters->eventId, fn (Builder $q, $id) => $q->where('event_id', $id))
            ->when($filters->createdBy, fn (Builder $q, $id) => $q->where('created_by', $id))
            ->when($filters->dueBefore, fn (Builder $q, $d) => $q->whereDate('due_date', '<=', $d))
            ->when($filters->dueAfter, fn (Builder $q, $d) => $q->whereDate('due_date', '>=', $d));

        // whereHas on the assignment pivot rather than a join: a task with three
        // assignees must appear once, and a join would return it three times.
        if ($filters->assignedTo) {
            $query->whereHas('assignments', fn (Builder $q) => $q
                ->where('user_id', $filters->assignedTo)
                ->where('role', TaskAssignmentRole::ASSIGNEE->value));
        }

        if ($filters->unassigned === true) {
            $query->whereDoesntHave('assignees');
        }

        if ($filters->overdue === true) {
            $query->overdue();
        } elseif ($filters->overdue === false) {
            $query->where(fn (Builder $q) => $q
                ->whereNull('due_date')
                ->orWhere('due_date', '>=', now()));
        }

        $this->applySearch($query, $filters->search);
        $this->applyGeo($query, $filters);

        return $query;
    }

    /**
     * Free-text search across the fields a coordinator actually types into.
     *
     * `reference` is matched exactly first — someone pasting TSK-2026-000042
     * wants that one task, not every task mentioning it. The remaining LIKE
     * clauses carry a leading wildcard and therefore cannot use an index; that
     * is an accepted cost at this data size. If the task table ever reaches the
     * point where search is the bottleneck, the fix is a FULLTEXT index on
     * (title, description) and MATCH…AGAINST here — the change stays inside
     * this method.
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        if (blank($search)) {
            return;
        }

        $term = str_replace(['%', '_'], ['\%', '\_'], $search);

        $query->where(function (Builder $q) use ($term) {
            $q->where('reference', $term)
                ->orWhere('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('location_name', 'like', "%{$term}%");
        });
    }

    /**
     * "Tasks near me", as a bounding box.
     *
     * A box, not a circle: it hits the composite (latitude, longitude) index,
     * where a haversine expression in the WHERE clause would force a full scan.
     * The box is a slight over-select — its corners reach further than the
     * radius — and the client refines to a true circle once the candidate set is
     * small. Longitude degrees narrow towards the poles, so the longitude span
     * is divided by cos(latitude); without that the box is far too wide in
     * Afghanistan's latitudes.
     */
    private function applyGeo(Builder $query, TaskFilters $filters): void
    {
        if (! $filters->hasGeoFilter()) {
            return;
        }

        $latDelta = $filters->radiusKm / 111.32;
        $cos = max(cos(deg2rad($filters->latitude)), 0.01); // guard against the poles
        $lngDelta = $filters->radiusKm / (111.32 * $cos);

        $query->withinBounds(
            $filters->latitude - $latDelta,
            $filters->latitude + $latDelta,
            $filters->longitude - $lngDelta,
            $filters->longitude + $lngDelta,
        );
    }

    /**
     * Ordering, from an allow-list.
     *
     * `urgency` is the default and is not a column: it is priority rank then
     * deadline, which is the order a coordinator actually works in. A secondary
     * sort on `id` keeps pagination stable — without it, rows sharing a sort
     * value can reshuffle between pages and the client shows duplicates.
     */
    private function applySort(Builder $query, TaskFilters $filters): Builder
    {
        if ($filters->sort === 'urgency') {
            return $query->mostUrgent()->orderBy('tasks.id');
        }

        $column = in_array($filters->sort, TaskFilters::SORTABLE, strict: true)
            ? $filters->sort
            : 'created_at';

        if ($column === 'priority') {
            // Alphabetical priority is meaningless ("critical" < "low"); rank it.
            return $query
                ->orderByRaw(TaskPriority::sqlOrderCase('priority', $filters->direction))
                ->orderBy('tasks.id');
        }

        return $query->orderBy($column, $filters->direction)->orderBy('tasks.id');
    }
}
