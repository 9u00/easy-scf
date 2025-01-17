<?php

return [
    'userModel' => 'user',
    'userInfo' => 'authInfo',
    'db' => [
        'read' => [
            'database_type' => 'mysql',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'database_name' => 'test',
            'port' => '3306',
        ],
        'write' => [
            'database_type' => 'mysql',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => '123456',
            'database_name' => 'test',
            'port' => '3306',
        ],
        'redis' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '123456',
        ]
    ]
];
