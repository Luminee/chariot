<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chariot Scripts Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure Chariot scripts directory.
    |
    */
    'scripts_dir' => base_path() . '/database/scripts/',

    /*
    |--------------------------------------------------------------------------
    | Chariot Signature Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure Chariot signature.
    |
    */
    'signature' => [

        'directory_separator' => '#',

        'connection_separator' => '@',
    ],
];
