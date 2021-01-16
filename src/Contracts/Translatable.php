<?php

namespace Amirami\Localizator\Contracts;

use Illuminate\Support\Collection;

/**
 * Class Translatable
 * @package Amirami\Localizator\Contracts
 */
abstract class Translatable extends Collection
{
    abstract public function sortAlphabetically(): Collection;
}
