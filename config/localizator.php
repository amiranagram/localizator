<?php

return [

    'localize' => [
        'default' => true,
        'json'    => true,
    ],

    /**
     * Search criteria for files.
     */
    'search'   => [
        /**
         * Directories which should be looked inside.
         */
        'dirs'      => ['resources/views'],

        /**
         * Patterns by which files should be queried.
         * The values can be a regular expresion, glob, or just a string.
         */
        'patterns'  => ['*.php'],

        /**
         * Functions that the strings will be extracted from.
         * Add here any custom defined functions.
         * NOTE: The translation string should always be the first argument.
         */
        'functions' => ['__', 'trans', '@lang']
    ],

    /**
     * Should the localize command sort extracted strings alphabetically?
     */
    'sort'     => true,

];
