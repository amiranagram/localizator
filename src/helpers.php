<?php

if (! function_exists('lang_path')) {
    /**
     * @param string $path
     * @return string
     */
    function lang_path($path = '')
    {
        return app()->langPath($path);
    }
}
