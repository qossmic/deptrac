<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\AstMap;
use DependencyTracker\DependencyResult;

interface DependencyEmitterInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @param AstMap $astMap
     * @param DependencyResult $dependencyResult
     * @return void
     */
    public function applyDependencies(AstMap $astMap, DependencyResult $dependencyResult);

}