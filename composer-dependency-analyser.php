<?php declare(strict_types = 1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPackage('composer/xdebug-handler', [ErrorType::UNUSED_DEPENDENCY]) // needed for e2e tests, no direct usage in code
    ->ignoreErrorsOnPath(__DIR__ . '/tests', [ErrorType::UNKNOWN_CLASS]); // keep ability to test invalid symbols
