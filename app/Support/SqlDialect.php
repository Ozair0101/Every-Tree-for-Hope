<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Date-truncation expressions, in whichever dialect the connection speaks.
 *
 * There is no portable SQL for "group these rows by month". MySQL wants
 * DATE_FORMAT, SQLite wants strftime, Postgres wants to_char — and getting it
 * wrong is not a loud failure: the query works perfectly in production and
 * throws "no such function" the moment the test suite runs on SQLite.
 *
 * That has now bitten this codebase three times — FIELD() in the task
 * repository, DATE_FORMAT in the trend chart, and ALTER COLUMN in a migration.
 * Centralising it means the next person writing an aggregate gets it right
 * without knowing the history.
 *
 * The column name is never caller-supplied in this codebase — every call site
 * passes a literal — so interpolating it carries no injection surface. Anything
 * dynamic must be allow-listed by the caller before it reaches here.
 */
class SqlDialect
{
    /** `YYYY-MM`, for grouping by month. */
    public static function month(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            'sqlsrv' => "FORMAT({$column}, 'yyyy-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    /** `YYYY-MM-DD`, for grouping by day. */
    public static function day(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "date({$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM-DD')",
            'sqlsrv' => "FORMAT({$column}, 'yyyy-MM-dd')",
            default => "DATE({$column})",
        };
    }

    /**
     * Whole hours between two timestamps, as a signed number.
     *
     * Used for completion times. Hours rather than minutes because a task that
     * takes three days is the norm here, and a five-digit minute count tells
     * nobody anything.
     */
    public static function hoursBetween(string $from, string $to): string
    {
        return match (DB::connection()->getDriverName()) {
            // Julian days × 24. SQLite has no native interval arithmetic.
            'sqlite' => "(julianday({$to}) - julianday({$from})) * 24",
            'pgsql' => "EXTRACT(EPOCH FROM ({$to} - {$from})) / 3600",
            'sqlsrv' => "DATEDIFF(hour, {$from}, {$to})",
            default => "TIMESTAMPDIFF(SECOND, {$from}, {$to}) / 3600",
        };
    }

    /**
     * Day of week as 0–6, Monday first.
     *
     * Every engine numbers weekdays differently and half of them start on
     * Sunday. Normalising here means the heat map's rows line up with its
     * labels whatever the database is.
     */
    public static function weekday(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            // strftime %w is 0=Sunday; shift so Monday is 0.
            'sqlite' => "((CAST(strftime('%w', {$column}) AS INTEGER) + 6) % 7)",
            'pgsql' => "((EXTRACT(DOW FROM {$column})::int + 6) % 7)",
            'sqlsrv' => "((DATEPART(weekday, {$column}) + 5) % 7)",
            // WEEKDAY() is already 0=Monday in MySQL.
            default => "WEEKDAY({$column})",
        };
    }

    /** Hour of day, 0–23. */
    public static function hourOfDay(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%H', {$column}) AS INTEGER)",
            'pgsql' => "EXTRACT(HOUR FROM {$column})::int",
            'sqlsrv' => "DATEPART(hour, {$column})",
            default => "HOUR({$column})",
        };
    }
}
