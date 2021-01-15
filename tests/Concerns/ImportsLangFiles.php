<?php

namespace Amirami\Localizator\Tests\Concerns;

/**
 * Trait ImportsLangFiles
 * @package Amirami\Localizator\Tests\Concerns
 */
trait ImportsLangFiles
{
    /**
     * @param string $fileName
     * @return string
     */
    protected function getLangFilePath(string $fileName): string
    {
        return resource_path('lang' . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @param string $locale
     * @param string $fileName
     * @return array
     * @noinspection PhpIncludeInspection
     */
    protected function getDefaultLangContents(string $locale, string $fileName): array
    {
        return require $this->getLangFilePath($locale . DIRECTORY_SEPARATOR . "{$fileName}.php");
    }

    /**
     * @param string $locale
     * @return array
     */
    protected function getJsonLangContents(string $locale): array
    {
        return json_decode(
            file_get_contents($this->getLangFilePath("{$locale}.json")),
            true
        );
    }
}
