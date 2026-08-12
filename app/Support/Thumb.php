<?php

namespace App\Support;

/**
 * Builds URLs for the on-the-fly thumbnail endpoint {@see \App\Http\Controllers\ThumbnailController}.
 *
 * Resources call this instead of returning the full-size image URL for a list
 * card, so the app downloads a few hundred kilobytes instead of several
 * megabytes per photograph.
 */
class Thumb
{
    /**
     * A thumbnail URL for a stored image path, or null when there is nothing to
     * thumbnail. An already-absolute URL (an external image) is returned as-is —
     * the endpoint only knows how to resize files on the local public disk.
     */
    public static function url(?string $path, int $width = 400): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // url() resolves against the current request host, so the app gets a
        // thumbnail on the same origin it called the API on.
        return url('/thumb/'.$width.'/'.ltrim($path, '/'));
    }
}
