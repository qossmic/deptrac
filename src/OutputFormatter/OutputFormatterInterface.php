<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\DependencyContext;
use Symfony\Component\Console\Output\OutputInterface;

interface OutputFormatterInterface
{
    /**
     * @return string used as an identifier to access to the formatter or to display something more user-friendly to the
     *                user when referring to the formatter
     *
     * @example "graphviz"
     */
    public function getName(): string;

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array;

    /**
     * Renders the final result.
     *
     * @param DependencyContext    $dependencyContext
     * @param OutputInterface      $output
     * @param OutputFormatterInput $outputFormatterInput
     *
     * @return void
     */
    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    );
}
