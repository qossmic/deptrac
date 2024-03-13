<?php declare(strict_types = 1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->addPathToScan(__DIR__ . '/deptrac.php', isDev: false)
    ->ignoreErrorsOnPath(__DIR__ . '/tests', [ErrorType::UNKNOWN_CLASS]); // keep ability to test invalid symbols
