<?php

namespace Amirami\Locale;

use Amirami\Locale\Commands\GenerateLocaleCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->app->bind(Localizator::class);
        $this->app->bind(Parser::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/locale.php' => config_path('locale.php'),
            ], 'config');

            $this->commands(GenerateLocaleCommand::class);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/locale.php', 'locale');
    }
}