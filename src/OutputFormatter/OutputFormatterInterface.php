<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\DependencyContext;
use Symfony\Component\Console\Output\OutputInterface;

interface OutputFormatterInterface
{
    public function getName();

    /** @return OutputFormatterOption[] */
    public function configureOptions();

    /**
     * @param DependencyContext    $dependencyContext
     * @param OutputInterface      $output
     * @param OutputFormatterInput $outputFormatterInput
     *
     * @return mixed
     */
    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    );
}
