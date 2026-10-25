<?php

namespace Nonamechrs\GotenbergScreenshots\Tests;

use Nonamechrs\GotenbergScreenshots\GotenbergScreenshotsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected ?array $templatePaths = null;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('screenshots.templates.paths', $this->templatePaths ?? [__DIR__.'/fixtures/templates']);
    }

    protected function getPackageProviders($app): array
    {
        return [GotenbergScreenshotsServiceProvider::class];
    }
}
