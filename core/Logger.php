<?php

namespace core;

// Свой кастомный логгер для ускорения разработки
class Logger
{
    private static string $logFile = __DIR__ . '/../../test/logs/app.log';

    public static function init(): void
    {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public static function log(string $message, string $level = 'INFO'): void
    {
        self::init();
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message\n";
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function error(string $message): void
    {
        self::log($message, 'ERROR');
    }

    public static function debug(string $message): void
    {
        self::log($message, 'DEBUG');
    }
}
