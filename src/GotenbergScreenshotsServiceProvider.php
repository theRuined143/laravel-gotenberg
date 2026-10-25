<?php

namespace Nonamechrs\GotenbergScreenshots;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Nonamechrs\GotenbergScreenshots\Http\Middleware\EnsureValidApiToken;

class GotenbergScreenshotsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/screenshots.php', 'screenshots');

        $this->app->singleton(GotenbergClient::class);
    }

    public function boot(): void
    {
        // Templates resolve as screenshots::<name>. Lookup order: the app's configured
        // template paths, then resources/views/vendor/screenshots, then the package's own.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'screenshots');

        foreach (array_reverse((array) config('screenshots.templates.paths')) as $path) {
            View::prependNamespace('screenshots', $path);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/screenshots.php' => config_path('screenshots.php')], 'screenshots-config');
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/screenshots'),
            ], 'screenshots-views');
        }

        if (config('screenshots.routes.enabled')) {
            $middleware = config('screenshots.routes.middleware') ?? ['api', EnsureValidApiToken::class];

            Route::middleware($middleware)
                ->prefix(config('screenshots.routes.prefix'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
