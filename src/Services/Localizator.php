<?php

namespace Amirami\Localizator\Services;

use Amirami\Localizator\Contracts\Collectable;
use Amirami\Localizator\Contracts\Translatable;
use Amirami\Localizator\Contracts\Writable;

class Localizator
{
    /**
     * @param Translatable $keys
     * @param string $type
     * @param string $locale
     * @return void
     */
    public function localize(Translatable $keys, string $type, string $locale): void
    {
        $this->getWriter($type)->put($locale, $this->collect($keys, $type, $locale));
    }

    /**
     * @param Translatable $keys
     * @param string $type
     * @param string $locale
     * @return Translatable
     */
    protected function collect(Translatable $keys, string $type, string $locale): Translatable
    {
        return $keys
            ->merge($this->getCollector($type)->getTranslated($locale))
            ->when(config('localizator.sort'), function (Translatable $keyCollection) {
                return $keyCollection->sortAlphabetically();
            });
    }

    /**
     * @param string $type
     * @return Writable
     */
    protected function getWriter(string $type): Writable
    {
        return app("localizator.writers.$type");
    }

    /**
     * @param string $type
     * @return Collectable
     */
    protected function getCollector(string $type): Collectable
    {
        return app("localizator.collector.$type");
    }
}
