<?php

namespace Amirami\Locale\Commands;

use Amirami\Locale\Localizator;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
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
     * @var Finder
     */
    protected $finder;

    /**
     * @var Localizator
     */
    protected $localizator;

    /**
     * GenerateLocaleCommand constructor.
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        parent::__construct();

        $this->finder = $finder;
        $this->localizator = new Localizator($this, $finder);
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $languages = $this->argument('lang')
            ? explode(',', $this->argument('lang'))
            : [config('app.locale')];

        foreach ($languages as $language) {
            $this->localizator->localize($language);

            $this->info('Translatable strings have been generated');
        }
    }

    /**
     * @return array|array[]
     */
    protected function getArguments(): array
    {
        return [
            [
                'lang', InputArgument::REQUIRED, 'dsadas'
            ],
        ];
    }
}