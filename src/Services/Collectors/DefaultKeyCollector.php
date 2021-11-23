<?php

namespace Amirami\Localizator\Services\Collectors;

use Amirami\Localizator\Collections\DefaultKeyCollection;
use Amirami\Localizator\Contracts\Collectable;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DefaultKeyCollector implements Collectable
{
    /**
     * @param string $locale
     * @return Collection
     */
    public function getTranslated(string $locale): Collection
    {
        $translated = new DefaultKeyCollection;

        $this->getFiles($locale)
            ->each(function (SplFileInfo $fileInfo) use ($locale, $translated) {
                $translated->put(
                    $fileInfo->getFilenameWithoutExtension(),
                    $this->requireFile($locale, $fileInfo)
                );
            });

        return $translated;
    }

    /**
     * @param string $locale
     * @return Collection
     */
    protected function getFiles(string $locale): Collection
    {
        $dir = resource_path('lang'.DIRECTORY_SEPARATOR.$locale);

        if (! file_exists($dir)) {
            if (! mkdir($dir, 0755) && ! is_dir($dir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }

            return new Collection;
        }

        return new Collection(
            (new Finder)->in($dir)->name('*.php')->files()
        );
    }

    /**
     * @param string $locale
     * @param SplFileInfo $fileInfo
     * @return array
     * @noinspection PhpIncludeInspection
     */
    protected function requireFile(string $locale, SplFileInfo $fileInfo): array
    {
        return require resource_path(
            'lang'.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$fileInfo->getRelativePathname()
        );
    }
}
