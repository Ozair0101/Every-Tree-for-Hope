<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Access is role-based since the is_admin column was removed. A user
        // may reach admin surfaces only if they hold at least one role.
        if (! $user || ! $user->roles()->exists()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
