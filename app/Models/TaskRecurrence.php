<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A schedule that stamps tasks out of a template.
 *
 * A scheduled job reads recurrences whose `next_run_at` has passed, creates a
 * real task, and advances the pointer. Generated tasks are ordinary rows in
 * `tasks` — editing one never touches the schedule, and deactivating the
 * schedule leaves existing work standing.
 *
 * Generation is idempotent through the unique
 * (task_recurrence_id, occurrence_date) index on `tasks`: a scheduler that runs
 * twice, or a replayed queue job, cannot duplicate an occurrence.
 */
class TaskRecurrence extends Model
{
    public const FREQUENCY_DAILY = 'daily';

    public const FREQUENCY_WEEKLY = 'weekly';

    public const FREQUENCY_MONTHLY = 'monthly';

    protected $fillable = [
        'task_template_id',
        'name',
        'frequency',
        'repeat_every',
        'weekdays',
        'day_of_month',
        'time_of_day',
        'starts_on',
        'ends_on',
        'max_occurrences',
        'occurrences_count',
        'latitude',
        'longitude',
        'location_name',
        'assignee_user_ids',
        'reviewer_user_ids',
        'last_generated_at',
        'next_run_at',
        'is_active',
    ];

    protected $casts = [
        'repeat_every' => 'integer',
        'weekdays' => 'array',
        'day_of_month' => 'integer',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'max_occurrences' => 'integer',
        'occurrences_count' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'assignee_user_ids' => 'array',
        'reviewer_user_ids' => 'array',
        'last_generated_at' => 'datetime',
        'next_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(TaskTemplate::class, 'task_template_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /** Due, active schedules — the generator's only query. */
    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now());
    }

    /**
     * Whether this schedule should still produce work.
     *
     * `max_occurrences` is a safety net as much as a feature: a misconfigured
     * rule cannot flood the table.
     */
    public function hasRemainingOccurrences(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->ends_on && $this->ends_on->isPast()) {
            return false;
        }

        return $this->max_occurrences === null
            || $this->occurrences_count < $this->max_occurrences;
    }

    /**
     * The occurrence after the given date, honouring frequency and weekday mask.
     *
     * Returns null once the schedule has run out.
     */
    public function nextOccurrenceAfter(CarbonImmutable $after): ?CarbonImmutable
    {
        if (! $this->hasRemainingOccurrences()) {
            return null;
        }

        $next = match ($this->frequency) {
            self::FREQUENCY_DAILY => $after->addDays($this->repeat_every),
            self::FREQUENCY_WEEKLY => $this->nextWeeklyOccurrence($after),
            self::FREQUENCY_MONTHLY => $this->nextMonthlyOccurrence($after),
            default => null,
        };

        if ($next === null) {
            return null;
        }

        if ($this->time_of_day) {
            [$hour, $minute] = array_map('intval', explode(':', (string) $this->time_of_day));
            $next = $next->setTime($hour, $minute);
        }

        return $this->ends_on && $next->gt($this->ends_on) ? null : $next;
    }

    /**
     * Monthly rules may pin a day of the month. `addMonthsNoOverflow` keeps
     * 31 January + 1 month at 28 February instead of spilling into March, and
     * the clamp handles a rule pinned to the 31st in a short month.
     */
    private function nextMonthlyOccurrence(CarbonImmutable $after): CarbonImmutable
    {
        $next = $after->addMonthsNoOverflow($this->repeat_every);

        if ($this->day_of_month) {
            $next = $next->day(min($this->day_of_month, $next->daysInMonth));
        }

        return $next;
    }

    /**
     * Weekly rules carry a weekday mask ([1,3,5] = Mon/Wed/Fri): step one day at
     * a time until a permitted weekday is hit, then apply the week interval.
     */
    private function nextWeeklyOccurrence(CarbonImmutable $after): CarbonImmutable
    {
        $weekdays = $this->weekdays ?: [];

        if ($weekdays === []) {
            return $after->addWeeks($this->repeat_every);
        }

        $candidate = $after->addDay();

        // At most 7 steps: some weekday in the mask always matches within a week.
        for ($i = 0; $i < 7; $i++) {
            if (in_array($candidate->dayOfWeekIso, $weekdays, strict: false)) {
                return $candidate;
            }
            $candidate = $candidate->addDay();
        }

        return $after->addWeeks($this->repeat_every);
    }
}
