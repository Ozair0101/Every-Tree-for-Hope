<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Per-request memo so repeated reads don't re-query the database.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $memo = null;

    /**
     * Read a setting value, falling back to $default when it is not set.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (static::$memo === null) {
            static::$memo = static::query()->pluck('value', 'key')->all();
        }

        return static::$memo[$key] ?? $default;
    }

    /**
     * Create or update a setting and refresh the in-memory cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$memo = null;
    }
}
