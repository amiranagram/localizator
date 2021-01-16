<?php

namespace Amirami\Localizator\Tests;

use Amirami\Localizator\ServiceProvider;
use Amirami\Localizator\Tests\Concerns\CreatesTestFiles;
use Amirami\Localizator\Tests\Concerns\ImportsLangFiles;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Finder\Finder;

/**
 * Class TestCase
 * @package Amirami\Localizator\Tests
 */
class TestCase extends Orchestra
{
    use CreatesTestFiles;
    use ImportsLangFiles;

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
     * This method is called after the last test of this test class is run.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // Flush one last time after all tests have finished running.
        self::flushDirectories('lang', 'views');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application|\Illuminate\Contracts\Foundation\Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application|\Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function getEnvironmentSetUp($app): void
    {
        $app->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'Mock');
    }

    /**
     * @param string $fileName
     * @param string $message
     * @return void
     */
    protected static function assertLangFileExists(string $fileName, string $message = ''): void
    {
        static::assertFileExists(
            resource_path('lang' . DIRECTORY_SEPARATOR . $fileName),
            $message
        );
    }

    /**
     * @param string|string[] $locales
     * @param array $fileNames
     * @param string $message
     */
    protected static function assertDefaultLangFilesExist($locales, array $fileNames, string $message = ''): void
    {
        $locales = is_array($locales) ? $locales : (array) $locales;

        foreach ($locales as $locale) {
            foreach ($fileNames as $fileName) {
                static::assertLangFileExists($locale . DIRECTORY_SEPARATOR . "{$fileName}.php", $message);
            }
        }
    }

    /**
     * @param string|string[] $locales
     * @param string $message
     * @return void
     */
    protected static function assertJsonLangFilesExist($locales, string $message = ''): void
    {
        $locales = is_array($locales) ? $locales : (array) $locales;

        foreach ($locales as $locale) {
            static::assertLangFileExists("{$locale}.json", $message);
        }
    }

    /**
     * Delete all files from selected directories in resources folder.
     *
     * @param string ...$dirNames
     * @return void
     */
    protected static function flushDirectories(...$dirNames): void
    {
        $dirNames = array_map(static function ($dirName) {
            return resource_path($dirName);
        }, $dirNames);

        $finder = (new Finder())->in($dirNames);
        $directories = [];

        foreach ($finder as $fileInfo) {
            if (file_exists($fileInfo->getRealPath())) {
                if (is_dir($fileInfo->getRealPath())) {
                    $directories[] = $fileInfo;
                } else {
                    unlink($fileInfo->getRealPath());
                }
            }
        }

        foreach ($directories as $directory) {
            rmdir($directory->getRealPath());
        }
    }
}
