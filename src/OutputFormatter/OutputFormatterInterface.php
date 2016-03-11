<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyResult;
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
