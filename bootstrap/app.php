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

        // API is stateless: force JSON responses and take the language from
        // the request itself (X-Locale / ?lang / Accept-Language) rather
        // than from a session.
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\SetApiLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API clients must never receive an HTML redirect to the login page.
        // Without this, an expired token on a mobile request would return a
        // 302 to /admin/login and the app would try to parse HTML as JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );

        /*
         | Business-rule failures from the task module.
         |
         | These are not faults — "this task already has its one assignee" is a
         | legitimate answer, and the client needs to show it to the user rather
         | than a stack trace. They carry their own status (422, or 403 for an
         | authorisation-shaped refusal) and a machine-readable `reason` so the
         | app can branch without parsing English.
         |
         | Deliberately narrow: only these two types are translated. Anything
         | else keeps bubbling to the 500 handler, where a real bug belongs.
        */
        $exceptions->render(function (\App\Services\Tasks\Exceptions\TaskOperationException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'reason' => $e->reason(),
                'errors' => null,
            ], $e->getStatusCode());
        });

        $exceptions->render(function (\App\Exceptions\TaskAssignmentException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'reason' => 'assignment_refused',
                'errors' => null,
            ], 422);
        });
    })->create();
