<?php

namespace Amirami\Localizator\Services;

use Amirami\Localizator\Collections\DefaultKeyCollection;
use Amirami\Localizator\Collections\JsonKeyCollection;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Parser
 * @package Amirami\Localizator\Services
 */
class Parser
{
    /**
     * @var array
     */
    private $config;

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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->defaultKeys = new DefaultKeyCollection;
        $this->jsonKeys = new JsonKeyCollection;
    }

    /**
     * @param Collection $files
     */
    public function parseKeys(Collection $files): void
    {
        $files
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
        return (bool)preg_match('/^[^.\s]\S*\.\S*[^.\s]$/', $key);
    }

    /**
     * @param SplFileInfo $file
     * @return Collection
     */
    protected function getStrings(SplFileInfo $file): Collection
    {
        $keys = new Collection;

        foreach ($this->config['search']['functions'] as $function) {
            if (
            preg_match_all($this->searchPattern($function), $file->getContents(), $matches)
            ) {
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
        return '/(' . $function . ')\(\h*[\'"](.+)[\'"]\h*[),]/U';
    }

    /**
     * @param string $locale
     * @param string $type
     * @return Collection
     */
    public function getKeys(string $locale, string $type): Collection
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
