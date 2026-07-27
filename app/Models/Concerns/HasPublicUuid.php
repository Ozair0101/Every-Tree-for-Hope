<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Gives a model a public UUID for API routing, keeping the bigint key internal.
 *
 * Two identifiers, each doing what it is good at. The auto-increment `id` stays
 * the primary key and every foreign key, because on InnoDB the primary key is
 * the clustered index and is copied into every secondary index — a random
 * 36-char UUID there means page splits on insert and fatter indexes forever. The
 * UUID is the outward-facing handle: a sequential id in a URL tells anyone who
 * looks how many records the organisation has, and invites walking the range.
 *
 * `Str::orderedUuid()` produces a time-ordered COMB UUID, so values created
 * close together sort close together and the unique index appends rather than
 * scattering writes across the B-tree. That difference matters at volume.
 */
trait HasPublicUuid
{
    public static function bootHasPublicUuid(): void
    {
        static::creating(function ($model) {
            $model->uuid ??= (string) Str::orderedUuid();
        });
    }

    /**
     * Route-model binding resolves on the UUID.
     *
     * Internal code that already holds an id should use `find()` directly rather
     * than going through the router's key.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function scopeByUuid(Builder $query, string $uuid): Builder
    {
        return $query->where($query->getModel()->getTable().'.uuid', $uuid);
    }
}
