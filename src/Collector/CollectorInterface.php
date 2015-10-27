<?php

namespace DependencyTracker\Collector;

use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;

interface CollectorInterface
{
    public function getType();

    public function applyAstFile(AstMap $astMap, DependencyResult $dependencyResult);
}