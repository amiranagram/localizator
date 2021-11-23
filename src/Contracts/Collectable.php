<?php

namespace Amirami\Localizator\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface Collectable.
 */
interface Collectable
{
    /**
     * @param string $locale
     * @return Collection
     */
    public function getTranslated(string $locale): Collection;
}
