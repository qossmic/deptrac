<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassNameLayerResolver;
use DependencyTracker\DependencyResult;

interface OutputFormatterInterface
{
    public function getName();

    public function finish(DependencyResult $dependencyResult, ClassNameLayerResolver $classNameLayerResolver);
}