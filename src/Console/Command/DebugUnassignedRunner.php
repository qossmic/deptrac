<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\UnassignedAnalyser;

/**
 * @internal Should only be used by DebugUnassignedCommand
 */
final class DebugUnassignedRunner
{
    private UnassignedAnalyser $analyser;
    private Loader $loader;

    public function __construct(UnassignedAnalyser $analyser, Loader $loader)
    {
        $this->analyser = $analyser;
        $this->loader = $loader;
    }

    public function run(DebugUnassignedOptions $options, Output $output): void
    {
        $configuration = $this->loader->load($options->getConfigurationFile());

        $output->writeLineFormatted($this->analyser->analyse($configuration));
    }
}
