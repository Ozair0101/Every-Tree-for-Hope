<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Role-based since is_admin was removed: a user must hold at least one role.
        if (! auth()->user()?->roles()->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
