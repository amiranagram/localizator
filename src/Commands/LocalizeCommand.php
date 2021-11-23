<?php

namespace Amirami\Localizator\Commands;

use Amirami\Localizator\Services\Localizator;
use Amirami\Localizator\Services\Parser;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class LocalizeCommand extends Command
{
    use ConfirmableTrait;

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
    protected $description = 'Generate locale files with strings found in scanned files.';

    /**
     * Execute the localize command.
     *
     * @param Localizator $localizator
     * @param Parser $parser
     * @return int
     */
    public function handle(Localizator $localizator, Parser $parser): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $locales = $this->getLocales();
        $progressBar = $this->output->createProgressBar(count($locales));

        $this->info('Localizing: '.implode(', ', $locales));

        $parser->parseKeys();

        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Localizing...');
        $progressBar->start();

        foreach ($locales as $locale) {
            $progressBar->setMessage("Localizing {$locale}...");

            foreach ($this->getTypes() as $type) {
                $localizator->localize($parser->getKeys($locale, $type), $type, $locale);
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info(
            "\nTranslatable strings have been generated for locale(s): ".implode(', ', $locales)
        );

        return 0;
    }

    /**
     * @return array
     */
    protected function getLocales(): array
    {
        return $this->argument('lang')
            ? explode(',', $this->argument('lang'))
            : [config('app.locale')];
    }

    /**
     * @return array
     */
    protected function getTypes(): array
    {
        return array_keys(array_filter(config('localizator.localize')));
    }
}
