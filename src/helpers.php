<?php

if (!function_exists('lang_path')) {
    /**
     * @param string $path
     * @return string
     */
    function lang_path($path = '')
    {
        if (
            function_exists('app') &&
            method_exists(app(), 'langPath') &&
            is_dir(app()->langPath())
        ) {
            return app()->langPath($path);
        } else {
            return resource_path('lang' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
        }
    }
}
