<?php

namespace Amirami\Localizator\Contracts;

/**
 * Interface Writable
 * @package Amirami\Localizator\Contracts
 */
interface Writable
{
    /**
     * @param string $locale
     * @param Translatable $keys
     */
    public function put(string $locale, Translatable $keys): void;
}
