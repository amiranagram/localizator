<?php

namespace Amirami\Localizator\Services\Writers;

use Amirami\Localizator\Contracts\Translatable;
use Amirami\Localizator\Contracts\Writable;
use Illuminate\Filesystem\Filesystem;

class JsonWriter implements Writable
{
    /**
     * @param string $locale
     * @param Translatable $keys
     */
    public function put(string $locale, Translatable $keys): void
    {
        $file = resource_path('lang'.DIRECTORY_SEPARATOR."{$locale}.json");

        (new Filesystem)->put(
            $file,
            $keys->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
