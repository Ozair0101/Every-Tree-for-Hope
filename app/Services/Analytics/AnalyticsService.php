<?php

namespace App\Services\Analytics;

use App\Enums\TaskAssignmentRole;
use App\Enums\TaskAssignmentStatus;
use App\Enums\TaskReviewStatus;
use App\Enums\TaskStatus;
use App\Enums\TaskSubmissionStatus;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskReview;
use App\Models\TaskSubmission;
use App\Models\Tree;
use App\Support\SqlDialect;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Every figure the analytics screens show.
 *
 * One class, because these numbers have to agree with each other: "average
 * completion time" on the dashboard and in the exported report must come from
 * the same definition, and the surest way to guarantee that is one place that
 * defines it.
 *
 * ── The definitions, stated once ─────────────────────────────────────────────
 * Almost every disagreement about a metric is really a disagreement about its
 * definition, so each one is written down beside the query rather than left to
 * be inferred from SQL.
 */
class AnalyticsService
{
    /**
     * Tasks completed per month.
     *
     * Keyed on `completed_at`, never `created_at`: a task raised in March and
     * finished in May is May's throughput. Using the creation date would credit
     * the wrong month and make a busy month look idle.
     *
     * @return Collection<int, array{period: string, label: string, completed: int, created: int}>
     */
    public function monthlyCompleted(int $months = 12): Collection
    {
        $since = now()->subMonths($months - 1)->startOfMonth();

        $completed = $this->monthlyCounts('completed_at', $since, fn ($q) => $q
            ->where('status', TaskStatus::APPROVED->value));

        $created = $this->monthlyCounts('created_at', $since);

        return collect(range($months - 1, 0))->map(function (int $ago) use ($completed, $created) {
            $month = now()->subMonths($ago);
            $key = $month->format('Y-m');

            return [
                'period' => $key,
                'label' => $month->format('M Y'),
                'completed' => (int) ($completed[$key] ?? 0),
                'created' => (int) ($created[$key] ?? 0),
            ];
        });
    }

    /**
     * How long work takes, from being started to being approved.
     *
     * Measured from `started_at`, not `assigned_at`. The gap before someone
     * starts is a scheduling problem, not a measure of how long the work takes,
     * and mixing the two produces an "average completion time" that mostly
     * reflects how quickly volunteers open the app.
     *
     * The median is reported alongside the mean because one abandoned task
     * finished three months late drags an average badly, and the median is what
     * actually describes a typical job.
     *
     * @return array{average_hours: ?float, median_hours: ?float, sample: int}
     */
    public function completionTime(?Carbon $since = null): array
    {
        $expression = SqlDialect::hoursBetween('started_at', 'completed_at');

        $hours = TaskAssignment::query()
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->where('status', TaskAssignmentStatus::APPROVED->value)
            ->when($since, fn ($q) => $q->where('completed_at', '>=', $since))
            ->selectRaw("{$expression} as hours")
            ->pluck('hours')
            ->map(fn ($value) => (float) $value)
            // A negative duration means the clocks disagreed, not that the work
            // finished before it started. Dropped rather than averaged in.
            ->filter(fn (float $value) => $value >= 0)
            ->sort()
            ->values();

        if ($hours->isEmpty()) {
            return ['average_hours' => null, 'median_hours' => null, 'sample' => 0];
        }

        return [
            'average_hours' => round($hours->avg(), 1),
            'median_hours' => round($this->median($hours), 1),
            'sample' => $hours->count(),
        ];
    }

    /**
     * Volunteers ranked by how they perform.
     *
     * ── Why this is not simply "most tasks completed" ────────────────────────
     * A volunteer given forty easy tasks would top a raw count while someone
     * given four hard ones, all done well and on time, would look idle. The
     * score blends three things a coordinator actually cares about:
     *
     *   completion rate  did they finish what they took on
     *   approval rate    was the work accepted first time
     *   punctuality      was it done before the deadline
     *
     * Volume is reported alongside but does not drive the ranking, and anyone
     * below `$minimumTasks` is excluded entirely — one perfect task is not
     * evidence of anything, and letting it top the table would make the whole
     * screen untrustworthy.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function performers(int $limit = 10, bool $ascending = false, int $minimumTasks = 3): Collection
    {
        $rows = TaskAssignment::query()
            ->where('task_assignments.role', TaskAssignmentRole::ASSIGNEE->value)
            ->join('users', 'users.id', '=', 'task_assignments.user_id')
            ->join('tasks', 'tasks.id', '=', 'task_assignments.task_id')
            ->leftJoin('task_reviews', 'task_reviews.task_assignment_id', '=', 'task_assignments.id')
            ->groupBy('users.id', 'users.name', 'users.lastname')
            ->select([
                'users.id',
                'users.name',
                'users.lastname',
                DB::raw('COUNT(DISTINCT task_assignments.id) as assigned'),
                // Every count here is DISTINCT over the assignment id, not a
                // plain SUM. The left join to task_reviews fans out one row per
                // review, so an assignment reviewed twice would otherwise be
                // counted twice — producing completion rates above 100%.
                DB::raw($this->countAssignmentsWhere("task_assignments.status = '".TaskAssignmentStatus::APPROVED->value."'").' as completed'),
                DB::raw($this->countAssignmentsWhere("task_assignments.status = '".TaskAssignmentStatus::DECLINED->value."'").' as declined'),
                DB::raw('AVG(task_reviews.score) as average_score'),
                DB::raw('AVG(task_reviews.rating) as average_rating'),
                // Finished before the deadline. A task with no deadline cannot
                // be late, so it is not counted either way.
                DB::raw($this->countAssignmentsWhere(
                    'task_assignments.completed_at IS NOT NULL '
                    .'AND tasks.due_date IS NOT NULL '
                    .'AND task_assignments.completed_at <= tasks.due_date'
                ).' as on_time'),
                DB::raw($this->countAssignmentsWhere(
                    'tasks.due_date IS NOT NULL AND task_assignments.completed_at IS NOT NULL'
                ).' as dated'),
            ])
            ->havingRaw('COUNT(DISTINCT task_assignments.id) >= ?', [$minimumTasks])
            ->get();

        return $rows
            ->map(function ($row) {
                $assigned = (int) $row->assigned;
                $completed = (int) $row->completed;
                $dated = (int) $row->dated;

                $completionRate = $assigned > 0 ? $completed / $assigned : 0;
                $punctuality = $dated > 0 ? ((int) $row->on_time) / $dated : null;
                $quality = $row->average_score !== null ? ((float) $row->average_score) / 100 : null;

                return [
                    'id' => $row->id,
                    'name' => trim($row->name.' '.($row->lastname ?? '')),
                    'assigned' => $assigned,
                    'completed' => $completed,
                    'declined' => (int) $row->declined,
                    // Percentages as whole integers, not floats: these land in
                    // Blade and in spreadsheet cells, and "100.0%" reads as
                    // spurious precision on a figure that has none.
                    'completion_rate' => (int) round($completionRate * 100),
                    'punctuality' => $punctuality === null ? null : (int) round($punctuality * 100),
                    'average_score' => $row->average_score === null ? null : round((float) $row->average_score, 1),
                    'average_rating' => $row->average_rating === null ? null : round((float) $row->average_rating, 1),
                    'score' => round($this->performanceScore($completionRate, $quality, $punctuality), 1),
                ];
            })
            ->sortBy('score', SORT_REGULAR, ! $ascending)
            ->take($limit)
            ->values();
    }

    /**
     * Blend the three rates into one 0–100 figure.
     *
     * Weighted toward completion because finishing the work is the point;
     * quality and punctuality shape the ranking among people who do finish.
     * A missing component is dropped and the remaining weights renormalised,
     * rather than treated as zero — a volunteer whose tasks carried no deadline
     * should not be marked down for it.
     */
    private function performanceScore(float $completion, ?float $quality, ?float $punctuality): float
    {
        $components = array_filter([
            [0.5, $completion],
            [0.3, $quality],
            [0.2, $punctuality],
        ], fn (array $pair) => $pair[1] !== null);

        $weight = array_sum(array_column($components, 0));

        if ($weight <= 0) {
            return 0;
        }

        $total = array_sum(array_map(fn (array $pair) => $pair[0] * $pair[1], $components));

        return ($total / $weight) * 100;
    }

    /**
     * Review scores and how they are distributed.
     *
     * The spread matters as much as the average: a mean of 70 made of scores
     * clustered at 70 describes a consistent programme, and the same mean made
     * of 40s and 100s describes a very different one.
     *
     * @return array<string, mixed>
     */
    public function reviewQuality(?Carbon $since = null): array
    {
        $reviews = TaskReview::query()
            ->when($since, fn ($q) => $q->where('reviewed_at', '>=', $since));

        $scores = (clone $reviews)->whereNotNull('score')->pluck('score')->map(fn ($s) => (float) $s);

        $verdicts = (clone $reviews)
            ->select('review_status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('review_status')
            ->pluck('aggregate', 'review_status');

        $total = (int) $verdicts->sum();

        return [
            'average_score' => $scores->isEmpty() ? null : round($scores->avg(), 1),
            'median_score' => $scores->isEmpty() ? null : round($this->median($scores->sort()->values()), 1),
            'average_rating' => round((float) (clone $reviews)->whereNotNull('rating')->avg('rating'), 2) ?: null,
            'total' => $total,
            'approved' => (int) ($verdicts[TaskReviewStatus::APPROVED->value] ?? 0),
            'rejected' => (int) ($verdicts[TaskReviewStatus::REJECTED->value] ?? 0),
            'needs_revision' => (int) ($verdicts[TaskReviewStatus::NEEDS_REVISION->value] ?? 0),
            'first_time_approval' => $total > 0
                ? (int) round(((int) ($verdicts[TaskReviewStatus::APPROVED->value] ?? 0)) / $total * 100)
                : null,
            // Buckets rather than a raw list — a histogram is what a reader can
            // actually interpret at a glance.
            'distribution' => $this->scoreDistribution($scores),
        ];
    }

    /** @return array<string, int> */
    private function scoreDistribution(Collection $scores): array
    {
        $buckets = ['0–20' => 0, '21–40' => 0, '41–60' => 0, '61–80' => 0, '81–100' => 0];

        foreach ($scores as $score) {
            $key = match (true) {
                $score <= 20 => '0–20',
                $score <= 40 => '21–40',
                $score <= 60 => '41–60',
                $score <= 80 => '61–80',
                default => '81–100',
            };

            $buckets[$key]++;
        }

        return $buckets;
    }

    /**
     * The review backlog, and how stale it is.
     *
     * The count alone hides the problem: five submissions waiting an hour is
     * healthy, five waiting three weeks is a programme losing its volunteers.
     *
     * @return array{count: int, oldest_days: ?int, over_a_week: int}
     */
    public function pendingReviews(): array
    {
        $pending = TaskSubmission::query()->where('status', TaskSubmissionStatus::PENDING->value);

        $oldest = (clone $pending)->min('created_at');

        return [
            'count' => (clone $pending)->count(),
            'oldest_days' => $oldest ? (int) Carbon::parse($oldest)->diffInDays(now()) : null,
            'over_a_week' => (clone $pending)->where('created_at', '<', now()->subWeek())->count(),
        ];
    }

    /**
     * Trees recorded, and how many have been followed up.
     *
     * The follow-up rate is the honest measure of a planting programme: trees
     * in the ground is an input, trees photographed alive months later is the
     * outcome.
     *
     * @return array<string, mixed>
     */
    public function treePlantation(int $months = 12): array
    {
        $since = now()->subMonths($months - 1)->startOfMonth();
        $expression = SqlDialect::month('planted_on');

        $monthly = Tree::query()
            ->approved()
            ->whereNotNull('planted_on')
            ->where('planted_on', '>=', $since)
            ->selectRaw("{$expression} as period, COUNT(*) as aggregate")
            ->groupBy('period')
            ->pluck('aggregate', 'period');

        $approved = Tree::query()->approved()->count();
        $withFollowUp = Tree::query()->withComparison()->count();

        return [
            'total' => Tree::query()->count(),
            'approved' => $approved,
            'pending' => Tree::query()->where('status', 'pending')->count(),
            'with_follow_up' => $withFollowUp,
            'follow_up_rate' => $approved > 0 ? (int) round($withFollowUp / $approved * 100) : null,
            'monthly' => collect(range($months - 1, 0))->map(function (int $ago) use ($monthly) {
                $month = now()->subMonths($ago);

                return [
                    'label' => $month->format('M Y'),
                    'count' => (int) ($monthly[$month->format('Y-m')] ?? 0),
                ];
            }),
        ];
    }

    /**
     * Activity by weekday and hour — the heat map.
     *
     * Answers a scheduling question no total can: when are volunteers actually
     * in the field? A programme that discovers all its work happens between
     * 06:00 and 10:00 should stop sending reminders at 14:00.
     *
     * Built from submissions rather than task creation, because that is the
     * moment work genuinely happened.
     *
     * @return array{grid: array<int, array<int, int>>, peak: int}
     */
    public function activityHeatMap(int $days = 90): array
    {
        $weekday = SqlDialect::weekday('created_at');
        $hour = SqlDialect::hourOfDay('created_at');

        $rows = TaskSubmission::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw("{$weekday} as weekday, {$hour} as hour, COUNT(*) as aggregate")
            ->groupByRaw("{$weekday}, {$hour}")
            ->get();

        // Zero-filled: a gap rendered as an absent cell would break the grid,
        // and a heat map with holes in it is unreadable.
        $grid = array_fill(0, 7, array_fill(0, 24, 0));
        $peak = 0;

        foreach ($rows as $row) {
            $day = (int) $row->weekday;
            $slot = (int) $row->hour;

            if ($day < 0 || $day > 6 || $slot < 0 || $slot > 23) {
                continue;
            }

            $count = (int) $row->aggregate;
            $grid[$day][$slot] = $count;
            $peak = max($peak, $count);
        }

        return ['grid' => $grid, 'peak' => $peak];
    }

    /**
     * Where the work happens — a geographic heat map, as ranked places.
     *
     * Grouped by `location_name` rather than by coordinate: a place a
     * coordinator can name is more actionable than a cluster centroid, and the
     * panel has no mapping key to plot a real density layer with.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function locationHotspots(int $limit = 10): Collection
    {
        return Task::query()
            ->whereNotNull('location_name')
            ->where('location_name', '!=', '')
            ->groupBy('location_name')
            ->select([
                'location_name',
                DB::raw('COUNT(*) as tasks'),
                DB::raw($this->countWhere('status', TaskStatus::APPROVED->value).' as completed'),
                DB::raw('AVG(latitude) as latitude'),
                DB::raw('AVG(longitude) as longitude'),
            ])
            ->orderByDesc('tasks')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'location' => $row->location_name,
                'tasks' => (int) $row->tasks,
                'completed' => (int) $row->completed,
                'completion_rate' => $row->tasks > 0 ? (int) round($row->completed / $row->tasks * 100) : 0,
                'latitude' => $row->latitude === null ? null : round((float) $row->latitude, 5),
                'longitude' => $row->longitude === null ? null : round((float) $row->longitude, 5),
            ]);
    }

    /** Everything the dashboard needs, in one call. */
    public function overview(): array
    {
        return [
            'monthly' => $this->monthlyCompleted(),
            'completion_time' => $this->completionTime(),
            'top_performers' => $this->performers(5),
            'lowest_performers' => $this->performers(5, ascending: true),
            'review_quality' => $this->reviewQuality(),
            'pending_reviews' => $this->pendingReviews(),
            'trees' => $this->treePlantation(),
            'heat_map' => $this->activityHeatMap(),
            'hotspots' => $this->locationHotspots(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function monthlyCounts(string $column, Carbon $since, ?callable $constrain = null): Collection
    {
        $expression = SqlDialect::month($column);

        $query = Task::query()
            ->whereNotNull($column)
            ->where($column, '>=', $since)
            ->selectRaw("{$expression} as period, COUNT(*) as aggregate")
            ->groupBy('period');

        if ($constrain) {
            $constrain($query);
        }

        return $query->pluck('aggregate', 'period');
    }

    /**
     * A portable conditional count.
     *
     * `SUM(CASE WHEN … )` rather than `COUNT(*) FILTER` or MySQL's shorthand
     * `SUM(col = value)` — both of which are dialect-specific, and one of which
     * has already caused a production-only failure in this codebase.
     */
    private function countWhere(string $column, string $value): string
    {
        return "SUM(CASE WHEN {$column} = '".addslashes($value)."' THEN 1 ELSE 0 END)";
    }

    /**
     * A conditional count that survives a fan-out join.
     *
     * Counts distinct assignment ids rather than rows, so a one-to-many join
     * (an assignment with several reviews) cannot inflate the figure. The
     * `ELSE NULL` is implicit — CASE without ELSE yields NULL, and COUNT skips
     * NULLs, which is exactly the behaviour wanted here.
     */
    private function countAssignmentsWhere(string $condition): string
    {
        return "COUNT(DISTINCT CASE WHEN {$condition} THEN task_assignments.id END)";
    }

    /** @param Collection<int, float> $sorted values, already sorted ascending */
    private function median(Collection $sorted): float
    {
        $count = $sorted->count();

        if ($count === 0) {
            return 0;
        }

        $middle = (int) floor($count / 2);

        return $count % 2 === 0
            ? ($sorted[$middle - 1] + $sorted[$middle]) / 2
            : $sorted[$middle];
    }
}
