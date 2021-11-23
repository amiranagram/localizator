<?php

namespace Amirami\Localizator\Contracts;

use Illuminate\Support\Collection;

/**
 * Class Translatable.
 */
abstract class Translatable extends Collection
{
    abstract public function sortAlphabetically(): Collection;
}
