<?php

namespace Amirami\Localizator\Services\Writers;

use Amirami\Localizator\Collections\DefaultKeyCollection;
use Amirami\Localizator\Contracts\Translatable;
use Amirami\Localizator\Contracts\Writable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DefaultWriter implements Writable
{
    /**
     * @var string
     */
    protected $tempUuid;

    public function __construct()
    {
        $this->tempUuid = Str::uuid();
    }

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
    public function exportArray(array $contents): string
    {
        $export = var_export($this->temporarilyModifyIntKeys($contents), true);

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

        return sprintf("<?php\n\nreturn %s;\n", str_replace("_$this->tempUuid", '', $export));
    }

    /**
     * @param string $locale
     * @param string $fileName
     * @return string
     */
    protected function getFile(string $locale, string $fileName): string
    {
        return lang_path($locale.DIRECTORY_SEPARATOR.$fileName.'.php');
    }

    /**
     * @param array $contents
     * @return array
     */
    public function temporarilyModifyIntKeys(array $contents): array
    {
        $collection = collect($contents)
            ->mapWithKeys(function ($value, $key) {
                if (is_int($key)) {
                    $key .= '_'.$this->tempUuid;
                }

                if (is_array($value)) {
                    $value = $this->temporarilyModifyIntKeys($value);
                }

                return [$key => $value];
            });

        return $collection->toArray();
    }
}
