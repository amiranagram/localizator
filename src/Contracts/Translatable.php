<?php

namespace Amirami\Localizator\Contracts;

use Illuminate\Support\Collection;

abstract class Translatable extends Collection
{
    abstract public function sortAlphabetically(): Collection;
}
