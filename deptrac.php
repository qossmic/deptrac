<?php

use Composer\XdebugHandler\XdebugHandler;
use Qossmic\Deptrac\Console\Application;

if (PHP_VERSION_ID < 70400) {
    echo 'Required at least PHP version 7.4.0, your version: '.PHP_VERSION."\n";
    exit(1);
}

(static function (): void {
    require_once __DIR__.'/vendor/autoload.php';
})();

(static function (): void {
    if (\file_exists($autoload = getcwd().'/vendor/autoload.php')) {
        include_once $autoload;
    }
})();

$xdebug = new XdebugHandler('DEPTRAC');
$xdebug->check();
unset($xdebug);

(new Application())->run();
