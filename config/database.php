<?php
return [
    'host' => getenv('DB_HOST') ?: 'mysql',
    'dbname' => getenv('DB_NAME') ?: 'test_db',
    'user' => getenv('DB_USER') ?: 'user',
    'password' => getenv('DB_PASS') ?: 'user',
    'charset' => 'utf8mb4',
];