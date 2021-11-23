<?php

namespace Amirami\Localizator\Collections;

use Amirami\Localizator\Contracts\Translatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DefaultKeyCollection extends Translatable
{
    /**
     * @return Collection
     */
    public function sortAlphabetically(): Collection
    {
        return $this->sortKeys(SORT_NATURAL | SORT_FLAG_CASE);
    }

    /**
     * @param mixed $items
     * @return static
     */
    public function merge($items): self
    {
        return parent::merge(Arr::dot($items));
    }
}
