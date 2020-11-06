<?php

namespace Amirami\Locale\Commands;

use Amirami\Locale\Localizator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class GenerateLocaleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:local {lang?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @return void
     */
    public function handle(): void
    {
        $languages = $this->argument('lang')
            ? explode(',', $this->argument('lang'))
            : [config('app.locale')];

        $localizator = app(Localizator::class);

        foreach ($languages as $language) {
            $localizator->localize($language);

            $this->info('Translatable strings have been generated for locale: ' . $language);
        }
    }

    /**
     * @return array|array[]
     */
    protected function getArguments(): array
    {
        return [
            ['lang', InputArgument::REQUIRED, 'dsadas'],
        ];
    }
}