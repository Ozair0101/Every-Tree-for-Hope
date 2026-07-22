<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Makes every API request behave as if the client sent
 * `Accept: application/json`, so validation failures and aborts render as
 * JSON instead of an HTML error page — even when the mobile client forgets
 * the header.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
