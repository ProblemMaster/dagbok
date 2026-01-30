<?php

return [
    'default' => env('DB_CONNECTION', 'sqlite'),

    'migrations' => 'migrations',

    'connections' => [
        'sqlite' => (function () {
            // Resolve DB_DATABASE to an absolute path when possible
            $dbEnv = env('DB_DATABASE');
            if ($dbEnv) {
                // If path is already absolute (unix or Windows), use it
                if (preg_match('/^(\/|[A-Za-z]:\\\\)/', $dbEnv)) {
                    $databasePath = $dbEnv;
                } else {
                    // Treat as relative to project root
                    $databasePath = database_path($dbEnv);
                }
            } else {
                $databasePath = database_path('database.sqlite');
            }

            return [
                'driver' => 'sqlite',
                'database' => $databasePath,
                'prefix' => '',
            ];
        })(),

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ],
    ],
];
