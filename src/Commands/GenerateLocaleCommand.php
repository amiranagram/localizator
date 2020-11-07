<?php

namespace Amirami\Localizator\Commands;

use Amirami\Localizator\Localizator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class GenerateLocaleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localize {lang?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate local files with strings found in scanned files.';

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

            $this->info('Translatable strings have been generated for locale: '.$language);
        }
    }

    /**
     * @return array|array[]
     */
    protected function getArguments(): array
    {
        return [
            ['lang', InputArgument::REQUIRED, 'Argument'],
        ];
    }
}
