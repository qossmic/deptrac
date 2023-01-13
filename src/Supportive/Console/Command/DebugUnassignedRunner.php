<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Core\Analyser\UnassignedTokenAnalyser;
use Qossmic\Deptrac\Core\Dependency\UnrecognizedTokenException;
use Qossmic\Deptrac\Core\InputCollector\InputException;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;
use Qossmic\Deptrac\Supportive\Console\Exception\AnalyseException;

/**
 * @internal Should only be used by DebugUnassignedCommand
 */
final class DebugUnassignedRunner
{
    public function __construct(private readonly UnassignedTokenAnalyser $processor)
    {
    }

    /**
     * @throws AnalyseException
     */
    public function run(OutputInterface $output): void
    {
        try {
            $unassignedTokens = $this->processor->findUnassignedTokens();
        } catch (UnrecognizedTokenException $e) {
            throw AnalyseException::unrecognizedToken($e);
        } catch (InvalidLayerDefinitionException $e) {
            throw AnalyseException::invalidLayerDefinition($e);
        } catch (InputException $e) {
            throw AnalyseException::invalidFileInput($e);
        }

        if ([] === $unassignedTokens) {
            $output->writeLineFormatted('There are no unassigned tokens.');

            return;
        }

        $output->writeLineFormatted($unassignedTokens);
    }
}
