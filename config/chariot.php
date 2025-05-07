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

    /*
    |--------------------------------------------------------------------------
    | Chariot Connection Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure Chariot connection, then them will be loaded in
    | the [database.connections] pool.
    |
    */
    'extra_connections' => [
        'dev' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            // Like [database.connections.mysql]
        ],

        'core' => [
            'dev' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', 'localhost'),
            ],

            'dev.read' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', 'localhost'),
            ],
        ],
    ],

];
