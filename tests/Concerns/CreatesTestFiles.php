<?php

namespace Amirami\Localizator\Tests\Concerns;

use Amirami\Localizator\Services\Writers\DefaultWriter;
use RuntimeException;

trait CreatesTestFiles
{
    /**
     * @param string $contents
     * @param string $fileName
     * @return void
     */
    protected function createTestFile(string $contents, string $fileName): void
    {
        file_put_contents(
            resource_path($fileName),
            $contents
        );
    }

    /**
     * @param string $contents
     * @param string $fileName
     * @return void
     */
    protected function createTestView(string $contents, string $fileName = 'test'): void
    {
        $this->createTestFile(
            $contents,
            'views'.DIRECTORY_SEPARATOR."{$fileName}.blade.php"
        );
    }

    /**
     * @param string $contents
     * @param string $fileName
     * @return void
     */
    protected function createTestLangFile(string $contents, string $fileName): void
    {
        file_put_contents(
            lang_path($fileName),
            $contents
        );
    }

    /**
     * @param array $contents
     * @param string $locale
     * @return void
     */
    protected function createTestJsonLangFile(array $contents, string $locale): void
    {
        $this->createTestLangFile(
            json_encode($contents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            "{$locale}.json"
        );
    }

    /**
     * @param array $contents
     * @param string $fileName
     * @param string $locale
     * @return void
     */
    protected function createTestDefaultLangFile(array $contents, string $fileName, string $locale): void
    {
        $writer = app(DefaultWriter::class);
        $export = $writer->exportArray($contents);
        $dir = lang_path($locale);

        if (! file_exists($dir) && ! mkdir($dir, 0755) && ! is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $this->createTestLangFile(
            $export,
            $locale.DIRECTORY_SEPARATOR."{$fileName}.php"
        );
    }
}
