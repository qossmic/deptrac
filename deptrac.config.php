<?php

use Qossmic\Deptrac\Core\Layer\Collector\CollectorType;
use Qossmic\Deptrac\Core\Layer\Collector\DirectoryCollector;

return static function (Symfony\Config\DeptracConfig $config): void {
    $config->paths(['./src']);

    $config
        ->layers('analyser')
        ->name('analyser')
        ->collectors()
        ->type(CollectorType::TYPE_DIRECTORY->value)
        ->set('value', 'src/Core/Analyser/.*');

    $config
        ->layers('dependency')
        ->name('dependency')
        ->collectors()
        ->type(CollectorType::TYPE_DIRECTORY->value)
        ->set('value', 'src/Core/Dependency/.*');


    $config->ruleset('analyser', ['result', 'layer', 'dependency']);
    $config->ruleset('dependency', ['ast']);

};
