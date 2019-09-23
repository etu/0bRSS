<?php

return [
    'debug' => false,                   // Display debug data
    'bcrypt-password-cost' => 10,       // bcrypt password cost

    'paths' => [
        'migrations' => __DIR__.'/migrations',
    ],

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'database',

        'database' => [
            'adapter' => 'pgsql',       // pgsql, mysql
            'host'    => 'localhost',   // Database hostname
            'name'    => '0bRSS',       // Database name
            'user'    => '0bRSS',       // Database username
            'pass'    => 'secret-pass', // Database password
            'port'    => 5432,          // Database port: 5432 for pgsql and 3306 for mysql
            'charset' => 'utf8',        // Charset, don't change this
        ],
    ],
];
