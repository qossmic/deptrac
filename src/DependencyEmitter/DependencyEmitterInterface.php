<?php

namespace DependencyTracker\DependencyEmitter;

use DependencyTracker\DependencyResult;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\AstRunner\AstParser\AstParserInterface;

interface DependencyEmitterInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @param AstParserInterface $astParser
     * @param AstMap $astMap
     * @param DependencyResult $dependencyResult
     * @return mixed
     */
    public function applyDependencies(AstParserInterface $astParser, AstMap $astMap, DependencyResult $dependencyResult);

}