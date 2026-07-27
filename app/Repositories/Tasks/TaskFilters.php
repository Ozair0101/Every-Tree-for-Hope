<?php

namespace App\Repositories\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Http\Request;

/**
 * The parsed, validated shape of a task list query.
 *
 * A value object rather than passing the Request into the repository. Three
 * reasons: the repository stays testable without an HTTP layer, the scheduler
 * and the admin panel can build the same query without faking a request, and
 * every value arriving at the query builder has already been narrowed to a
 * known type — which is what keeps `sort` from ever reaching SQL as free text.
 */
class TaskFilters
{
    /**
     * Sortable columns, as an allow-list.
     *
     * Never interpolate a client-supplied column into ORDER BY. Beyond the
     * injection risk, an unindexed sort column turns a paginated list into a
     * filesort over the whole table — the allow-list is as much a performance
     * boundary as a security one. Each of these is either indexed or cheap.
     */
    public const SORTABLE = [
        'due_date', 'created_at', 'priority', 'title', 'progress', 'status', 'reference',
    ];

    public function __construct(
        /** @var array<int, string> */
        public readonly array $statuses = [],
        /** @var array<int, string> */
        public readonly array $priorities = [],
        public readonly ?int $categoryId = null,
        public readonly ?int $eventId = null,
        public readonly ?int $assignedTo = null,
        public readonly ?int $createdBy = null,
        public readonly ?string $search = null,
        public readonly ?bool $overdue = null,
        public readonly ?string $dueBefore = null,
        public readonly ?string $dueAfter = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?float $radiusKm = null,
        public readonly ?bool $unassigned = null,
        public readonly string $sort = 'urgency',
        public readonly string $direction = 'asc',
        public readonly bool $withTrashed = false,
    ) {}

    /**
     * Build from an already-validated request.
     *
     * Validation belongs to the FormRequest; this only maps names. Anything
     * that reaches here has passed `IndexTaskRequest::rules()`.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            statuses: self::toArray($request->query('status')),
            priorities: self::toArray($request->query('priority')),
            categoryId: $request->filled('category_id') ? (int) $request->query('category_id') : null,
            eventId: $request->filled('event_id') ? (int) $request->query('event_id') : null,
            assignedTo: $request->filled('assigned_to') ? (int) $request->query('assigned_to') : null,
            createdBy: $request->filled('created_by') ? (int) $request->query('created_by') : null,
            search: $request->filled('search') ? trim((string) $request->query('search')) : null,
            overdue: $request->filled('overdue') ? $request->boolean('overdue') : null,
            dueBefore: $request->query('due_before'),
            dueAfter: $request->query('due_after'),
            latitude: $request->filled('latitude') ? (float) $request->query('latitude') : null,
            longitude: $request->filled('longitude') ? (float) $request->query('longitude') : null,
            radiusKm: $request->filled('radius_km') ? (float) $request->query('radius_km') : null,
            unassigned: $request->filled('unassigned') ? $request->boolean('unassigned') : null,
            sort: (string) $request->query('sort', 'urgency'),
            direction: strtolower((string) $request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc',
        );
    }

    /** A copy scoped to one user's own work — the "My Tasks" screen. */
    public function forAssignee(int $userId): self
    {
        return new self(
            statuses: $this->statuses,
            priorities: $this->priorities,
            categoryId: $this->categoryId,
            eventId: $this->eventId,
            assignedTo: $userId,
            createdBy: $this->createdBy,
            search: $this->search,
            overdue: $this->overdue,
            dueBefore: $this->dueBefore,
            dueAfter: $this->dueAfter,
            latitude: $this->latitude,
            longitude: $this->longitude,
            radiusKm: $this->radiusKm,
            unassigned: $this->unassigned,
            sort: $this->sort,
            direction: $this->direction,
        );
    }

    public function hasGeoFilter(): bool
    {
        return $this->latitude !== null && $this->longitude !== null && $this->radiusKm !== null;
    }

    /** Only enum values that actually exist reach the query. */
    public function validStatuses(): array
    {
        return array_values(array_filter(
            $this->statuses,
            fn (string $s) => TaskStatus::tryFrom($s) !== null,
        ));
    }

    public function validPriorities(): array
    {
        return array_values(array_filter(
            $this->priorities,
            fn (string $p) => TaskPriority::tryFrom($p) !== null,
        ));
    }

    /** Accepts `?status=draft&status=assigned` and `?status=draft,assigned`. */
    private static function toArray(mixed $value): array
    {
        if (blank($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            'trim',
            is_array($value) ? $value : explode(',', (string) $value),
        )));
    }
}
