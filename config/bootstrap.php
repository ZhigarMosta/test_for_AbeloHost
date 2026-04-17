<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    $prefixes = [
        'config\\' => __DIR__ . '/',
        'core\\' => __DIR__ . '/../core/',
        'models\\' => __DIR__ . '/../models/',
        'controllers\\' => __DIR__ . '/../controllers/',
    ];
    
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) continue;
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) require $file;
    }
});

use core\Database;
use core\SmartySetup;

$db = Database::getInstance();
$smarty = SmartySetup::getInstance();
