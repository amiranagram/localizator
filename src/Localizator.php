<?php

namespace Amirami\Localizator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Localizator
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * Localizator constructor.
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param string $language
     *
     * @throws \JsonException
     *
     * @return bool
     */
    public function localize(string $language): bool
    {
        $strings = $this->parseStrings(
            $this->findAndCollectFiles()
        );

        $translated = $this->getExisting($language);

        $strings = $strings->merge($translated);

        if (config('localizator.sort')) {
            $strings = $this->sortAlphabetically($strings);
        }

        return $this->writeToFile(
            $language,
            $strings->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    protected function parseStrings(Collection $files): Collection
    {
        $parser = app(Parser::class);
        $parsedStrings = collect();

        $files
            ->map(function (SplFileInfo $file) use ($parser) {
                return $parser->getStrings($file);
            })
            ->flatten()
            ->each(function (string $string) use ($parsedStrings) {
                $parsedStrings->put($string, $string);
            });

        return $parsedStrings;
    }

    /**
     * @return Collection
     */
    protected function findAndCollectFiles(): Collection
    {
        $config = config('localizator.search');
        $directories = array_map(static function ($dir) {
            return base_path($dir);
        }, $config['dirs']);

        return new Collection(
            $this->finder->in($directories)->name($config['patterns'])->files()
        );
    }

    /**
     * @param string $language
     *
     * @throws \JsonException
     *
     * @return Collection
     */
    protected function getExisting(string $language): Collection
    {
        $locale = resource_path('lang/'.$language.'.json');

        if (! file_exists($locale)) {
            return collect();
        }

        return collect(
            json_decode(file_get_contents($locale), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @param Collection $strings
     *
     * @return Collection
     */
    protected function sortAlphabetically(Collection $strings): Collection
    {
        return $strings->sortBy(function ($item, $key) {
            return $key;
        }, SORT_STRING);
    }

    /**
     * @param string $language
     * @param string $contents
     *
     * @return bool
     */
    protected function writeToFile(string $language, string $contents): bool
    {
        $file = resource_path('lang/'.$language.'.json');

        return (bool) (new Filesystem())->put($file, $contents);
    }
}
