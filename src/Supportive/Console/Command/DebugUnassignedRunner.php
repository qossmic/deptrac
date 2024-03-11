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
     *
     * @return bool are there any unassigned tokens?
     */
    public function run(OutputInterface $output): bool
    {
        try {
            $unassignedTokens = $this->analyser->findUnassignedTokens();
        } catch (AnalyserException $e) {
            throw CommandRunException::analyserException($e);
        }

        if ([] === $unassignedTokens) {
            $output->writeLineFormatted('There are no unassigned tokens.');

            return false;
        }

        $output->writeLineFormatted($unassignedTokens);

        return true;
    }
}
