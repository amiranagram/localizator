<?php

namespace Amirami\Localizator\Services;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

/**
 * Class FileFinder
 * @package Amirami\Localizator\Services
 */
class FileFinder
{
    /**
     * @var array
     */
    private $config;

    /**
     * FileFinder constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        $directories = array_map(static function ($dir) {
            return base_path($dir);
        }, $this->config['search']['dirs']);

        return new Collection(
            (new Finder)->in($directories)->name($this->config['search']['patterns'])->files()
        );
    }
}
