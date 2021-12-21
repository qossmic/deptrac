<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Configuration\Loader;
use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\TokenAnalyser;
use function natcasesort;

/**
 * @internal Should only be used by DebugTokenCommand
 */
final class DebugTokenRunner
{
    private TokenAnalyser $analyser;
    private Loader $loader;

    public function __construct(TokenAnalyser $analyser, Loader $loader)
    {
        $this->analyser = $analyser;
        $this->loader = $loader;
    }

    public function run(DebugTokenOptions $options, Output $output): void
    {
        $configuration = $this->loader->load($options->getConfigurationFile());

        $layers = $this->analyser->analyse($configuration, $options->getToken());

        natcasesort($layers);

        $output->writeLineFormatted($layers);
    }
}
