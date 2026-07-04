<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
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
