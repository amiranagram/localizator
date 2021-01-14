<?php

namespace Amirami\Localizator\Services;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class FileFinder
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        $finder = new Finder;
        $directories = array_map(static function ($dir) {
            return base_path($dir);
        }, $this->config['search']['dirs']);

        return new Collection(
            $finder->in($directories)->name($this->config['search']['patterns'])->files()
        );
    }
}
