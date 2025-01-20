<?php

return [
    'userModel' => 'user',
    'userInfo' => 'authInfo',
    'db' => [
        'read' => [
            'database_type' => 'mysql',
            'server' => $_ENV['DB_READ_HOST'],
            'username' => $_ENV['DB_READ_USER'],
            'password' => $_ENV['DB_READ_PASSWORD'],
            'database_name' => $_ENV['DB_READ_NAME'],
            'port' => $_ENV['DB_READ_PORT'],
        ],
        'write' => [
            'database_type' => 'mysql',
            'server' => $_ENV['DB_HOST'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'database_name' => $_ENV['DB_NAME'],
            'port' => $_ENV['DB_PORT'],
        ],
        'redis' => [
            'host' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'password' => $_ENV['REDIS_PASSWORD'],
        ],
        'jwt' => [
            'secret' => $_ENV['JWT_SECRET'],
        ]
    ]
];
