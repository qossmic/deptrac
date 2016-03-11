<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\DependencyResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

interface OutputFormatterInterface
{
    public function getName();

    /** @return OutputFormatterOption[] */
    public function configureOptions();

    public function finish(
        AstMap $astMap,
        array $violations,
        DependencyResult $dependencyResult,
        ClassNameLayerResolverInterface $classNameLayerResolver,
        OutputInterface $output
    );
}
