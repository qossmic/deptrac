<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Core\Analyser\UnassignedTokenAnalyser;

/**
 * @internal Should only be used by DebugUnassignedCommand
 */
final class DebugUnassignedRunner
{
    private UnassignedTokenAnalyser $processor;

    public function __construct(UnassignedTokenAnalyser $processor)
    {
        $this->processor = $processor;
    }

    public function run(OutputInterface $output): void
    {
        $unassignedTokens = $this->processor->findUnassignedTokens();
        if ([] === $unassignedTokens) {
            $output->writeLineFormatted('There are no unassigned tokens.');

            return;
        }

        $output->writeLineFormatted($unassignedTokens);
    }
}
