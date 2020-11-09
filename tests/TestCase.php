<?php

namespace Amirami\Localizator\Tests;

use Amirami\Localizator\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'mock');

        $app['config']->set('app.locale', 'en');

        $app['config']->set('localizator.search.dirs', ['resources']);
        $app['config']->set('localizator.search.patterns', ['*.blade.php']);
        $app['config']->set('localizator.search.functions', ['__', 'trans', '@lang']);
    }
}
