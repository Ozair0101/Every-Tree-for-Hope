<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API clients must never receive an HTML redirect to the login page.
        // Without this, an expired token on a mobile request would return a
        // 302 to /admin/login and the app would try to parse HTML as JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );
    })->create();
