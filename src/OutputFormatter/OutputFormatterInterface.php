<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassNameLayerResolverInterface;
use DependencyTracker\DependencyResult;
use SensioLabs\AstRunner\AstMap;

interface OutputFormatterInterface
{
    public function getName();

    public function finish(AstMap $astMap, array $violations, DependencyResult $dependencyResult, ClassNameLayerResolverInterface $classNameLayerResolver);
}