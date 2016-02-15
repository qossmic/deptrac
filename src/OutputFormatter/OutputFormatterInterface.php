<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassNameLayerResolverInterface;
use DependencyTracker\DependencyResult;

interface OutputFormatterInterface
{
    public function getName();

    public function finish(DependencyResult $dependencyResult, ClassNameLayerResolverInterface $classNameLayerResolver);
}