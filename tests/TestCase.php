<?php

namespace Amirami\Localizator\Tests;

use Amirami\Localizator\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
    }
}
