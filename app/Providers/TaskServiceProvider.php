<?php

namespace App\Providers;

use App\Models\Task;
use App\Repositories\Contracts\TaskNotificationRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Tasks\EloquentTaskNotificationRepository;
use App\Repositories\Tasks\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Wiring for the task module.
 *
 * The repository bindings are what make the contracts worth having: controllers
 * type-hint the interface, so the query layer can be swapped — for a
 * cached decorator, or a fake in a test — without touching a controller.
 *
 * Bound as singletons: these hold no request state, so one instance per request
 * is enough and saves resolving them again for each controller that asks.
 */
class TaskServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TaskRepositoryInterface::class, EloquentTaskRepository::class);
        $this->app->singleton(TaskNotificationRepositoryInterface::class, EloquentTaskNotificationRepository::class);
    }

    public function boot(): void
    {
        // Explicit binding so `{task}` resolves on the public UUID and a request
        // carrying an integer id gets a 404 rather than someone else's task.
        // getRouteKeyName() covers implicit binding, but stating it here means
        // the rule survives a future refactor of the trait.
        \Illuminate\Support\Facades\Route::bind('task', function (string $value) {
            return Task::query()->byUuid($value)->firstOrFail();
        });
    }
}
