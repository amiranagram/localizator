<?php

namespace Amirami\Localizator\Tests;

use Amirami\Localizator\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Finder\Finder;

class TestCase extends Orchestra
{
    /**
     * @var string[]
     */
    protected $searchDirectories = ['resources/views'];

    /**
     * @var string[]
     */
    protected $searchPatterns = ['*.php'];

    /**
     * @var string[]
     */
    protected $searchFunctions = ['__', 'trans', '@lang'];

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
        $app->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'Mock');

        $app['config']->set('app.locale', 'en');

        $app['config']->set('localizator.search.dirs', $this->searchDirectories);
        $app['config']->set('localizator.search.patterns', $this->searchPatterns);
        $app['config']->set('localizator.search.functions', $this->searchFunctions);
    }

    /**
     * Delete all files from selected directories in resources folder.
     *
     * @param array|string[] ...$dirNames
     *
     * @return void
     */
    protected function cleanDirectories(...$dirNames): void
    {
        $dirNames = array_map(static function ($dirName) {
            return resource_path($dirName);
        }, $dirNames);

        $files = (new Finder())->in($dirNames)->files();

        foreach ($files as $file) {
            unlink($file->getPathname());
        }
    }
}
