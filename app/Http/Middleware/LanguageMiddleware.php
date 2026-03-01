<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageMiddleware
{
    /**
     * Available languages and their RTL status
     */
    protected $languages = [
        'en' => ['name' => 'English', 'rtl' => false],
        'fa' => ['name' => 'Dari', 'rtl' => true],
        'ps' => ['name' => 'Pashto', 'rtl' => true],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get language from URL parameter, session, or default
        $locale = $this->getLocale($request);
        
        // Set application locale
        App::setLocale($locale);
        
        // Store locale in session
        Session::put('locale', $locale);
        
        // Share language info with views
        view()->share([
            'current_locale' => $locale,
            'current_language' => $this->languages[$locale]['name'],
            'is_rtl' => $this->languages[$locale]['rtl'],
            'available_languages' => $this->languages,
        ]);

        return $next($request);
    }

    /**
     * Determine the locale from request or session
     */
    protected function getLocale(Request $request): string
    {
        // Check URL parameter first
        if ($request->has('lang') && array_key_exists($request->get('lang'), $this->languages)) {
            return $request->get('lang');
        }

        // Check session
        if (Session::has('locale') && array_key_exists(Session::get('locale'), $this->languages)) {
            return Session::get('locale');
        }

        // Check browser preference
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? '', 0, 2);
        if (array_key_exists($browserLocale, $this->languages)) {
            return $browserLocale;
        }

        // Default to English
        return 'en';
    }
}
