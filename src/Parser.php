<?php

namespace Amirami\Localizator;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class Parser
{
    public function getStrings(SplFileInfo $file): Collection
    {
        if (
            ! preg_match_all('/(__)\(\h*[\'"](.+)[\'"]\h*[),]/U', $file->getContents(), $matches)
        ) {
            return collect([]);
        }

        return collect($matches[2])->unique();
    }
}
