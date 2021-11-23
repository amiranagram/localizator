<?php

namespace Amirami\Localizator;

use Amirami\Localizator\Commands\LocalizeCommand;
use Amirami\Localizator\Services\Collectors\DefaultKeyCollector;
use Amirami\Localizator\Services\Collectors\JsonKeyCollector;
use Amirami\Localizator\Services\Localizator;
use Amirami\Localizator\Services\Writers\DefaultWriter;
use Amirami\Localizator\Services\Writers\JsonWriter;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/localizator.php' => config_path('localizator.php'),
            ], 'config');

            $this->commands(LocalizeCommand::class);
        }
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/localizator.php', 'localizator');

        $this->registerContainerClasses();
    }

    /**
     * @return void
     */
    private function registerContainerClasses(): void
    {
        $this->app->singleton('localizator', Localizator::class);

        $this->app->bind('localizator.writers.default', DefaultWriter::class);
        $this->app->bind('localizator.writers.json', JsonWriter::class);

        $this->app->bind('localizator.collector.default', DefaultKeyCollector::class);
        $this->app->bind('localizator.collector.json', JsonKeyCollector::class);
    }
}
