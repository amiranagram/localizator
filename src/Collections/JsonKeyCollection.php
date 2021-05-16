<?php

namespace Amirami\Localizator\Collections;

use Amirami\Localizator\Contracts\Translatable;
use Illuminate\Support\Collection;

class JsonKeyCollection extends Translatable
{
    public function sortAlphabetically(): Collection
    {
        return $this->sortKeys(SORT_NATURAL | SORT_FLAG_CASE);
    }
}
