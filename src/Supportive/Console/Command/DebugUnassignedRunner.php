<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Core\Analyser\AnalyserException;
use Qossmic\Deptrac\Core\Analyser\UnassignedTokenAnalyser;

/**
 * @internal Should only be used by DebugUnassignedCommand
 */
final class DebugUnassignedRunner
{
    public function __construct(private readonly UnassignedTokenAnalyser $analyser) {}

    /**
     * @throws CommandRunException
     */
    public function run(OutputInterface $output): void
    {
        try {
            $unassignedTokens = $this->analyser->findUnassignedTokens();
        } catch (AnalyserException $e) {
            throw CommandRunException::analyserException($e);
        }

        if ([] === $unassignedTokens) {
            $output->writeLineFormatted('There are no unassigned tokens.');

            return;
        }

        $output->writeLineFormatted($unassignedTokens);
    }
}
