<?php

namespace Amirami\Localizator\Services\Writers;

use Amirami\Localizator\Collections\DefaultKeyCollection;
use Amirami\Localizator\Contracts\Translatable;
use Amirami\Localizator\Contracts\Writable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class DefaultWriter implements Writable
{
    /**
     * @param string $locale
     * @param Translatable $keys
     */
    public function put(string $locale, Translatable $keys): void
    {
        $this->elevate($keys)
            ->each(function ($contents, $fileName) use ($locale) {
                $file = $this->getFile($locale, $fileName);

                (new Filesystem)->put(
                    $file,
                    $this->exportArray($contents)
                );
            });
    }

    /**
     * @param Translatable $keys
     * @return DefaultKeyCollection
     */
    protected function elevate(Translatable $keys): DefaultKeyCollection
    {
        $elevated = [];

        $keys->each(function ($value, $key) use (&$elevated) {
            Arr::set($elevated, $key, $value);
        });

        return new DefaultKeyCollection($elevated);
    }

    /**
     * @param array $contents
     * @return string
     */
    protected function exportArray(array $contents): string
    {
        $export = var_export($contents, true);

        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$1$2 => $3',
        ];

        $export = preg_replace(
            array_keys($patterns),
            array_values($patterns),
            $export
        );

        return sprintf("<?php\n\nreturn %s;\n", $export);
    }

    /**
     * @param string $locale
     * @param string $fileName
     * @return string
     */
    protected function getFile(string $locale, string $fileName): string
    {
        return resource_path('lang'.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$fileName.'.php');
    }
}
