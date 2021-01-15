<?php

namespace Amirami\Localizator\Commands;

use Amirami\Localizator\Facades\Localizator;
use Illuminate\Console\Command;

/**
 * Class LocalizeCommand
 * @package Amirami\Localizator\Commands
 */
class LocalizeCommand extends Command
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
    protected $description = 'Generate locale files with strings found in scanned files.';

    /**
     * Execute the localize command.
     *
     * @return void
     */
    public function handle(): void
    {
        $locales = $this->getLocales();
        $progressBar = $this->output->createProgressBar(count($locales));

        $this->info('Localizing: ' . implode(', ', $locales));

        $files = app('localizator.finder')->getFiles();
        $parser = app('localizator.parser');

        $parser->parseKeys($files);

        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Localizing...');
        $progressBar->start();

        foreach ($locales as $locale) {
            $progressBar->setMessage("Localizing {$locale}...");

            foreach ($this->getTypes() as $type) {
                Localizator::localize($parser->getKeys($locale, $type), $type, $locale);
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info(
            "\nTranslatable strings have been generated for locale(s): " . implode(', ', $locales)
        );
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
