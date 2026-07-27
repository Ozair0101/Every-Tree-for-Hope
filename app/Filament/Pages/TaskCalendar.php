<?php

namespace App\Filament\Pages;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * A month view of everything that falls due.
 *
 * Built as a plain Blade grid rather than by pulling in a calendar library:
 * the whole requirement is "which days are heavy and what is late", and a
 * month grid answers that in about eighty lines with no JavaScript dependency
 * to keep current.
 *
 * Tasks are placed by `due_date` — a calendar of deadlines, not of start dates.
 * A coordinator plans around when work must be finished.
 */
class TaskCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Task Calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Field Operations';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.task-calendar';

    /** The month being viewed, as Y-m. Lives in the query string so a view is linkable. */
    public string $month;

    public ?string $statusFilter = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_any_task') ?? false;
    }

    public function mount(): void
    {
        $this->month = request()->query('month', now()->format('Y-m'));
    }

    public function previousMonth(): void
    {
        $this->month = $this->current()->subMonthNoOverflow()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->current()->addMonthNoOverflow()->format('Y-m');
    }

    public function today(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function current(): Carbon
    {
        // Falls back to this month rather than throwing: `month` comes from the
        // query string, and a hand-edited URL should not be a 500.
        try {
            return Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    /**
     * The weeks to render, each a list of seven days.
     *
     * Padded to whole weeks so the grid is always rectangular — the leading and
     * trailing days belong to the neighbouring months and are dimmed in the view.
     *
     * @return array<int, array<int, array{date: Carbon, in_month: bool, tasks: Collection}>>
     */
    public function weeks(): array
    {
        $start = $this->current();
        $gridStart = $start->copy()->startOfWeek();
        $gridEnd = $start->copy()->endOfMonth()->endOfWeek();

        $tasks = $this->tasksBetween($gridStart, $gridEnd);

        $weeks = [];
        $cursor = $gridStart->copy();

        while ($cursor <= $gridEnd) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $key = $cursor->toDateString();

                $week[] = [
                    'date' => $cursor->copy(),
                    'in_month' => $cursor->month === $start->month,
                    'is_today' => $cursor->isToday(),
                    'tasks' => $tasks->get($key, collect()),
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * Every task due in the visible range, grouped by day.
     *
     * One query for the whole grid, grouped in memory — six weeks of separate
     * per-day queries would be forty-two round trips to render one screen.
     *
     * @return Collection<string, Collection<int, Task>>
     */
    private function tasksBetween(Carbon $from, Carbon $to): Collection
    {
        return Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($this->statusFilter, fn ($q, $status) => $q->where('status', $status))
            ->with('category:id,name,color')
            ->select(['id', 'uuid', 'reference', 'title', 'status', 'priority', 'due_date', 'task_category_id', 'progress'])
            ->orderBy('due_date')
            ->get()
            ->groupBy(fn (Task $task) => $task->due_date->toDateString());
    }

    /** Totals for the header strip. */
    public function monthSummary(): array
    {
        $start = $this->current();
        $end = $start->copy()->endOfMonth();

        $tasks = Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start, $end])
            ->get(['status', 'due_date']);

        return [
            'total' => $tasks->count(),
            'overdue' => $tasks->filter(fn (Task $t) => $t->is_overdue)->count(),
            'completed' => $tasks->where('status', TaskStatus::APPROVED)->count(),
        ];
    }

    /** @return array<string, string> */
    public function statusOptions(): array
    {
        return ['' => 'All statuses'] + TaskStatus::options();
    }

    public function priorityColor(TaskPriority $priority): string
    {
        return $priority->color();
    }
}
