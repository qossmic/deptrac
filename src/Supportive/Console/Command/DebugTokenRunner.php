<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Core\Analyser\LayerForTokenAnalyser;
use Qossmic\Deptrac\Core\Analyser\TokenType;
use Qossmic\Deptrac\Core\Dependency\UnrecognizedTokenException;
use Qossmic\Deptrac\Core\InputCollector\InputException;
use Qossmic\Deptrac\Core\Layer\Exception\InvalidLayerDefinitionException;
use Qossmic\Deptrac\Supportive\Console\Exception\AnalyseException;

use function implode;
use function sprintf;

/**
 * @internal Should only be used by DebugTokenCommand
 */
final class DebugTokenRunner
{
    public function __construct(private readonly LayerForTokenAnalyser $processor)
    {
    }

    /**
     * @throws AnalyseException
     */
    public function run(string $tokenName, TokenType $tokenType, OutputInterface $output): void
    {
        try {
            $matches = $this->processor->findLayerForToken($tokenName, $tokenType);
        } catch (UnrecognizedTokenException $e) {
            throw AnalyseException::unrecognizedToken($e);
        } catch (InvalidLayerDefinitionException $e) {
            throw AnalyseException::invalidLayerDefinition($e);
        } catch (InputException $e) {
            throw AnalyseException::invalidFileInput($e);
        }

        if ([] === $matches) {
            $output->writeLineFormatted(sprintf('Could not find a token matching "%s"', $tokenName));

            return;
        }

        $headers = ['matching token', 'layers'];
        $rows = [];
        foreach ($matches as $token => $layers) {
            $rows[] = [$token, [] !== $layers ? implode(', ', $layers) : '---'];
        }

        $output->getStyle()->table($headers, $rows);
    }
}
