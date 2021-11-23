<?php

namespace Amirami\Localizator\Services;

use Amirami\Localizator\Collections\DefaultKeyCollection;
use Amirami\Localizator\Collections\JsonKeyCollection;
use Amirami\Localizator\Contracts\Translatable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

class Parser
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var FileFinder
     */
    private $finder;

    /**
     * @var DefaultKeyCollection
     */
    private $defaultKeys;

    /**
     * @var JsonKeyCollection
     */
    private $jsonKeys;

    /**
     * Parser constructor.
     *
     * @param Repository $config
     * @param FileFinder $finder
     */
    public function __construct(Repository $config, FileFinder $finder)
    {
        $this->config = $config->get('localizator');
        $this->finder = $finder;
        $this->defaultKeys = new DefaultKeyCollection;
        $this->jsonKeys = new JsonKeyCollection;
    }

    /**
     * @return void
     */
    public function parseKeys(): void
    {
        $this->finder
            ->getFiles()
            ->map(function (SplFileInfo $file) {
                return $this->getStrings($file);
            })
            ->flatten()
            ->map(function (string $string) {
                return stripslashes($string);
            })
            ->each(function (string $string) {
                if ($this->isDotKey($string)) {
                    $this->defaultKeys->push($string);
                } else {
                    $this->jsonKeys->push($string);
                }
            });
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isDotKey($key): bool
    {
        return (bool) preg_match('/^[^.\s]\S*\.\S*[^.\s]$/', $key);
    }

    /**
     * @param SplFileInfo $file
     * @return Collection
     */
    protected function getStrings(SplFileInfo $file): Collection
    {
        $keys = new Collection;

        foreach ($this->config['search']['functions'] as $function) {
            if (preg_match_all($this->searchPattern($function), $file->getContents(), $matches)) {
                $keys->push($matches[2]);
            }
        }

        return $keys->count() ? $keys->flatten()->unique() : $keys;
    }

    /**
     * @param string $function
     * @return string
     */
    protected function searchPattern(string $function): string
    {
        return '/('.$function.')\(\h*[\'"](.+)[\'"]\h*[),]/U';
    }

    /**
     * @param string $locale
     * @param string $type
     * @return Translatable
     */
    public function getKeys(string $locale, string $type): Translatable
    {
        switch ($type) {
            case 'default':
                return $this->defaultKeys->combine(
                    $this->combineValues($locale, $type, $this->defaultKeys)
                );
            case 'json':
                return $this->jsonKeys->combine(
                    $this->combineValues($locale, $type, $this->jsonKeys)
                );
        }

        throw new RuntimeException('Export type not recognized! Only recognized types are "default" and "json".');
    }

    /**
     * @param string $locale
     * @param string $type
     * @param Collection $values
     * @return Collection
     */
    protected function combineValues(string $locale, string $type, Collection $values): Collection
    {
        if ($type === 'default' || $locale !== config('app.locale')) {
            return (new Collection)->pad($values->count(), '');
        }

        return $values;
    }
}
