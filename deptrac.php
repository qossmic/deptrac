<?php

use SensioLabs\Deptrac\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

require __DIR__.'/vendor/autoload.php';

if (PHP_VERSION_ID < 70200) {
    echo 'Required at least PHP version 7.2.0, your version: '.PHP_VERSION."\n";
    exit(1);
}

(new Application())->run();
