<?php

namespace Amirami\Localizator\Services\Collectors;

use Amirami\Localizator\Collections\JsonKeyCollection;
use Amirami\Localizator\Contracts\Collectable;
use Illuminate\Support\Collection;

class JsonKeyCollector implements Collectable
{
    /**
     * @param string $locale
     * @return Collection
     */
    public function getTranslated(string $locale): Collection
    {
        $file = resource_path('lang'.DIRECTORY_SEPARATOR."{$locale}.json");

        if (! file_exists($file)) {
            return new JsonKeyCollection;
        }

        return new JsonKeyCollection(
            json_decode(file_get_contents($file), true)
        );
    }
}
