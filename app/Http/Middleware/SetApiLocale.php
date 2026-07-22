<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The web site keeps the chosen language in the session. An API is
 * stateless, so the client tells us the language on every request.
 *
 * Resolution order (first match wins):
 *   1. ?lang=fa            — handy for links and quick testing
 *   2. X-Locale: fa        — the header the mobile app should send
 *   3. Accept-Language: fa — standard fallback
 *   4. 'en'
 *
 * Setting the locale here is what makes translatable models (job titles,
 * FAQs, upcoming events) and __('messages.*') strings come back in the
 * right language.
 */
class SetApiLocale
{
    /** Languages the project ships translations for. */
    public const SUPPORTED = ['en', 'fa', 'ps'];

    public function handle(Request $request, Closure $next): Response
    {
        $candidates = [
            $request->query('lang'),
            $request->header('X-Locale'),
            substr((string) $request->header('Accept-Language'), 0, 2),
        ];

        foreach ($candidates as $candidate) {
            $locale = strtolower(trim((string) $candidate));

            if (in_array($locale, self::SUPPORTED, true)) {
                app()->setLocale($locale);

                return $next($request);
            }
        }

        app()->setLocale('en');

        return $next($request);
    }
}
