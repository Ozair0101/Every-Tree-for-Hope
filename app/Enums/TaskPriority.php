<?php

namespace App\Enums;

/**
 * How urgent a task is.
 *
 * Stored as the lowercase string value so the column stays readable in raw SQL
 * and never breaks when a case is renamed in the UI. `weight()` gives a stable
 * numeric ordering for "most urgent first" queries without a second column.
 */
enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::CRITICAL => 'Critical',
        };
    }

    /** Hex colour for the mobile badge and the Filament table. */
    public function color(): string
    {
        return match ($this) {
            self::LOW => '#6b7280',
            self::MEDIUM => '#3b82f6',
            self::HIGH => '#f59e0b',
            self::CRITICAL => '#dc2626',
        };
    }

    /**
     * Higher = more urgent. Used by `Task::scopeMostUrgent()` for ordering and
     * by the reminder scheduler to decide how early to start nagging.
     */
    public function weight(): int
    {
        return match ($this) {
            self::LOW => 10,
            self::MEDIUM => 20,
            self::HIGH => 30,
            self::CRITICAL => 40,
        };
    }

    /**
     * Hours before `due_date` at which the "due soon" push should fire.
     * Critical work gets a longer runway.
     */
    public function reminderLeadHours(): int
    {
        return match ($this) {
            self::LOW => 24,
            self::MEDIUM => 24,
            self::HIGH => 48,
            self::CRITICAL => 72,
        };
    }

    /**
     * A portable ORDER BY expression that ranks priorities correctly.
     *
     * Alphabetical order is meaningless here — "critical" sorts before "low"
     * before "medium" — so the rank has to be expressed in SQL. `FIELD()` is
     * the obvious MySQL answer and was the original one, but it does not exist
     * in SQLite, which is what the test suite runs on: the ordering was correct
     * in production and unrunnable in tests. A CASE expression is standard SQL
     * and behaves identically everywhere.
     *
     * Built from `weight()` so the ranking cannot drift from the enum, and
     * from `cases()` so a new priority is included automatically. Nothing here
     * is caller-supplied, so there is no injection surface.
     */
    public static function sqlOrderCase(string $column = 'priority', string $direction = 'desc'): string
    {
        $whens = '';

        foreach (self::cases() as $case) {
            $whens .= " WHEN '{$case->value}' THEN {$case->weight()}";
        }

        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';

        return "CASE {$column}{$whens} ELSE 0 END {$direction}";
    }

    /** @return array<string, string> value => label, for select inputs. */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
