<?php

if (! function_exists('lang_path')) {
    /**
     * @param string $path
     * @return string
     */
    function lang_path($path = '')
    {
        return version_compare(app()->version(), '9.0', '>=')
            ? app()->langPath($path)
            : resource_path('lang'.($path !== '' ? DIRECTORY_SEPARATOR.$path : ''));
    }
}
