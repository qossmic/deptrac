<?php

use Qossmic\Deptrac\Core\Layer\Collector\CollectorType;
use Qossmic\Deptrac\Core\Layer\Collector\DirectoryCollector;

return static function (Symfony\Config\DeptracConfig $config): void {
    $config
        ->layers()
        ->name('analyser')
        ->collectors()
        ->type(DirectoryCollector::class)
        ->value('src/Core/Analyser/.*');

    $config
        ->layers()
        ->name('dependency')
        ->collectors()
        ->type(DirectoryCollector::class)
        ->value('src/Core/Dependency/.*');

    $config->ruleset('analyser', ['result', 'layer', 'dependency']);
    $config->ruleset('dependency', ['ast']);

};
