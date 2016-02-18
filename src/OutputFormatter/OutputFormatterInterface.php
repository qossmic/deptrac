<?php

namespace DependencyTracker\OutputFormatter;

use DependencyTracker\ClassNameLayerResolverInterface;
use DependencyTracker\DependencyResult;
use SensioLabs\AstRunner\AstMap;
use Symfony\Component\Console\Output\OutputInterface;

interface OutputFormatterInterface
{
    public function getName();

    public function finish(
        AstMap $astMap,
        array $violations,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver,
        OutputInterface $output
    );
}