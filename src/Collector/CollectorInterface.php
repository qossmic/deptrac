<?php

namespace DependencyTracker\Collector;

use DependencyTracker\AstMap;
use DependencyTracker\Configuration\ConfigurationLayer;
use DependencyTracker\DependencyResult;

interface CollectorInterface
{
    public function getType();

    public function applyAstFile(
        AstMap $astMap,
        DependencyResult $dependencyResult,
        ConfigurationLayer $layer,
        array $configuration
    );
}