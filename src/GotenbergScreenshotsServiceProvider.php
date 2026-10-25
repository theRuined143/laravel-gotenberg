<?php

namespace Nonamechrs\GotenbergScreenshots;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GotenbergScreenshotsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/screenshots.php', 'screenshots');

        $this->app->singleton(GotenbergClient::class);
    }

    public function boot(): void
    {
        // Templates resolve as screenshots::templates.<name>; files in
        // resources/views/vendor/screenshots/templates of the host app take precedence.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'screenshots');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/screenshots.php' => config_path('screenshots.php')], 'screenshots-config');
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/screenshots'),
            ], 'screenshots-views');
        }

        if (config('screenshots.routes.enabled')) {
            Route::middleware(config('screenshots.routes.middleware'))
                ->prefix(config('screenshots.routes.prefix'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
