<?php

if (! function_exists('lang_path')) {
    /**
     * @param string $path
     * @return string
     */
    function lang_path($path = '')
    {
        return resource_path('lang'.($path !== '' ? DIRECTORY_SEPARATOR.$path : ''));
    }
}
