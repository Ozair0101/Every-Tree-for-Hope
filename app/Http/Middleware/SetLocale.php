<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', 'en');
        
        // Validate locale
        if (!in_array($locale, ['en', 'fa', 'ps'])) {
            $locale = 'en';
        }
        
        app()->setLocale($locale);
        
        // Share locale with all views
        view()->share('locale', $locale);
        view()->share('isRTL', in_array($locale, ['fa', 'ps']));
        
        return $next($request);
    }
}
