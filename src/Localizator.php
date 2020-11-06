<?php

namespace Amirami\Locale;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Localizator
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Command $command, Finder $finder)
    {
        $this->command = $command;
        $this->finder = $finder;
    }

    public function localize(string $language)
    {
        $strings = $this->parseStrings(
            $this->findAndCollectFiles()
        );

        $translated = $this->getExisting($language);

        $strings = $strings->merge($translated);

        if (config('locale.sort')) {
            $strings = $this->sortAlphabetically($strings);
        }

        $this->writeToFile(
            $language, $strings->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    protected function findAndCollectFiles(): Collection
    {
        $config = config('locale.search');

        return new Collection(
            $this->finder->in($config['dirs'])->name($config['patterns'])->files()
        );
    }

    protected function parseStrings(Collection $collection): Collection
    {
        return collect();
    }

    protected function getExisting(string $language): Collection
    {
        return collect();
    }

    protected function sortAlphabetically(Collection $strings): Collection
    {
        return collect();
    }

    protected function writeToFile(string $language, string $contents): void
    {
        $file = resource_path('lang/' . $language . '.json');

        (new Filesystem())->put($file, $contents);
    }
}