<?php

namespace App\Providers;

use App\Models\Setting;
use App\Notifications\Channels\ExpoPushChannel;
use App\Notifications\Channels\TaskInboxChannel;
use App\Notifications\Channels\TaskPushChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Fallback donate link used if the settings table/row is unavailable.
     */
    private const DEFAULT_DONATE_URL = '#?campaign=camp_01KWF62PF5VRFFG9P753VMPMGG';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use clean pagination without backgrounds
        Paginator::defaultView('pagination::clean');
        Paginator::defaultSimpleView('pagination::clean');

        // The `expo` notification channel. Adding it to a notification's via()
        // is all that is then needed to have it delivered to the phone as well
        // as written to the database.
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('expo', fn ($app) => $app->make(ExpoPushChannel::class));

            // The task module's two channels. `task-inbox` writes the in-app
            // entry; `task-push` queues one delivery per registered device.
            // They are separate so a user who has muted pushes still gets the
            // record — muting a buzz is not muting the news.
            $service->extend('task-inbox', fn ($app) => $app->make(TaskInboxChannel::class));
            $service->extend('task-push', fn ($app) => $app->make(TaskPushChannel::class));
        });

        // Make the admin-editable donate link available to every view as $donateUrl.
        // Guarded so it never breaks the app before the settings table is migrated.
        try {
            $donateUrl = Setting::get('donate_url', self::DEFAULT_DONATE_URL);
        } catch (\Throwable $e) {
            $donateUrl = self::DEFAULT_DONATE_URL;
        }

        View::share('donateUrl', $donateUrl);
    }
}
