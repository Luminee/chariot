<?php

return [
    'conn_conf' => base_path() . '/.conn.conf.php',

    'script_namespace' => 'App\Console\Commands\\',

    'multi_pdo' => [
        'project' => [
            'online' => [
                'r' => 'online_read',
                'w' => 'online_write'
            ]
        ]
    ],

];