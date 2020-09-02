<?php

use Composer\XdebugHandler\XdebugHandler;
use SensioLabs\Deptrac\Console\Application;

require __DIR__.'/vendor/autoload.php';

if (PHP_VERSION_ID < 70205) {
    echo 'Required at least PHP version 7.2.5, your version: '.PHP_VERSION."\n";
    exit(1);
}

$autoloaderInWorkingDirectory = getcwd().'/vendor/autoload.php';

if (is_file($autoloaderInWorkingDirectory)) {
    require_once $autoloaderInWorkingDirectory;
}

$xdebug = new XdebugHandler('DEPTRAC', '--ansi');
$xdebug->check();
unset($xdebug);

(new Application())->run();
