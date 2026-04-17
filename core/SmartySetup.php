<?php

namespace core;

use Smarty;
use config\Smarty as SmartyConfig;

class SmartySetup
{
    private static ?Smarty $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): Smarty
    {
        if (self::$instance === null) {
            $config = SmartyConfig::fromArray();
            
            self::$instance = new Smarty();
            self::$instance->setTemplateDir($config['template_dir']);
            self::$instance->setCompileDir($config['compile_dir']);
            self::$instance->setCacheDir($config['cache_dir']);
            self::$instance->setDebugging($config['debug']);
            self::$instance->setCaching($config['caching']);
        }
        return self::$instance;
    }
}
