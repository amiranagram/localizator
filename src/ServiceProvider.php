<?php

namespace Amirami\Localizator;

use Amirami\Localizator\Commands\LocalizeCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->app->bind(Localizator::class);
        $this->app->bind(Parser::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/localizator.php' => config_path('localizator.php'),
            ], 'config');

            $this->commands(LocalizeCommand::class);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/localizator.php', 'localizator');
    }
}
