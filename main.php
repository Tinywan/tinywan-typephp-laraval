<?php

use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;

function main(): void
{
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    // 标记已被 TypePHP 静态编译的全局函数文件，避免 Composer autoloader 二次 require 报错
    $GLOBALS['__composer_autoload_files']['476ca15b8d69b04665cd879be9cb4c68'] = true; // Collections/functions.php
    $GLOBALS['__composer_autoload_files']['f57d353b41eb2e234b26064d63d8c5dd'] = true; // Filesystem/functions.php

    try {
        $basePath = getcwd();
        if (file_exists($basePath . '/vendor/autoload.php')) {
            require_once $basePath . '/vendor/autoload.php';
        }
        /** @var Application $app */
        $app = require $basePath . '/bootstrap/app.php';

        $status = $app->handleCommand(new ArgvInput);
        exit($status);
    } catch (\Throwable $e) {
        echo "MAIN FATAL ERROR: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
        exit(1);
    }
}
