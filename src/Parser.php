<?php

namespace Amirami\Localizator;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class Parser
{
    public function getStrings(SplFileInfo $file): Collection
    {
        $functions = config('localizator.search.functions');
        $strings = collect();

        foreach ($functions as $function) {
            if (
                preg_match_all($this->searchPattern($function), $file->getContents(), $matches)
            ) {
                $strings->push($matches[2]);
            }
        }

        return $strings->count() ? $strings->flatten()->unique() : $strings;
    }

    protected function searchPattern(string $function): string
    {
        return '/('.$function.')\(\h*[\'"](.+)[\'"]\h*[),]/U';
    }
}
