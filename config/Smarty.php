<?php
namespace config;

class Smarty
{
    public static function fromArray(): array
    {
        return require __DIR__ . '/smarty.php';
    }
}
