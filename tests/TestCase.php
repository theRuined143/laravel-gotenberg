<?php

namespace Nonamechrs\GotenbergScreenshots\Tests;

use Nonamechrs\GotenbergScreenshots\GotenbergScreenshotsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [GotenbergScreenshotsServiceProvider::class];
    }
}
