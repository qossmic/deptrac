<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Console\Command;

use Qossmic\Deptrac\Analyser\LayerForTokenAnalyser;
use Qossmic\Deptrac\Analyser\TokenType;
use Qossmic\Deptrac\OutputFormatter\Output;
use function implode;
use function sprintf;

/**
 * @internal Should only be used by DebugTokenCommand
 */
final class DebugTokenRunner
{
    private LayerForTokenAnalyser $processor;

    public function __construct(LayerForTokenAnalyser $processor)
    {
        $this->processor = $processor;
    }

    public function run(string $tokenName, TokenType $tokenType, Output $output): void
    {
        $matches = $this->processor->findLayerForToken($tokenName, $tokenType);

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
