<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

interface OutputFormatterInterface
{
    public function getName();

    /** @return OutputFormatterOption[] */
    public function configureOptions();

    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output
    );
}
