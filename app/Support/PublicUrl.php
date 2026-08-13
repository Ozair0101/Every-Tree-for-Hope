<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Builds a public `/storage/…` URL from the host the request actually arrived
 * on, rather than the `asset()` helper.
 *
 * `asset()` resolves against APP_URL (or a forced root / the scheme Laravel
 * *detects*), which is fragile on shared hosting behind an SSL-terminating
 * proxy: it can emit `http://localhost/...` or a plain-`http` URL that an
 * https app then refuses to load as mixed content. The tree endpoints already
 * avoid this by composing the URL from `$request->getSchemeAndHttpHost()`, and
 * their images load in production where the event/voice ones did not. This is
 * that same approach, shared so every resource resolves images identically.
 */
class PublicUrl
{
    public static function for(Request $request, ?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        // An already-absolute URL (an externally hosted image) is left alone.
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return $request->getSchemeAndHttpHost().'/storage/'.ltrim($path, '/');
    }
}
