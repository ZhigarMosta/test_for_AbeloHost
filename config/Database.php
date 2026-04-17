<?php
namespace config;

class Database
{
    public string $host;
    public string $dbname;
    public string $user;
    public string $password;
    public string $charset;

    public static function fromArray(): array
    {
        return require __DIR__ . '/database.php';
    }
}
