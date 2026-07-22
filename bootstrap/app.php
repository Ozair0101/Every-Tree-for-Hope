<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // JSON API for the mobile app — served under /api, named api.*
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'filament.admin' => \App\Http\Middleware\FilamentAdmin::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'language' => \App\Http\Middleware\LanguageMiddleware::class,

            // Spatie route guards for web/api routes — use as
            // ->middleware('role:Admin') or ->middleware('permission:view_any_donator').
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        
        $middleware->web(\App\Http\Middleware\SetLocale::class);
        $middleware->web(\App\Http\Middleware\LanguageMiddleware::class);

        // API is stateless: force JSON responses and take the language from
        // the request itself (X-Locale / ?lang / Accept-Language) rather
        // than from a session.
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\SetApiLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
