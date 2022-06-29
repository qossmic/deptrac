<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Contract\OutputFormatter\Output;
use Qossmic\Deptrac\Core\Analyser\TokenInLayerAnalyser;
use function array_map;

/**
 * @internal Should only be used by DebugLayerCommand
 */
final class DebugLayerRunner
{
    private TokenInLayerAnalyser $processor;

    /**
     * @var array<array{name: string, collectors: array<array<string, string|array<string, string>>>}>
     */
    private array $layers;

    /**
     * @param array<array{name: string, collectors: array<array<string, string|array<string, string>>>}> $layers
     */
    public function __construct(TokenInLayerAnalyser $processor, array $layers)
    {
        $this->processor = $processor;
        $this->layers = $layers;
    }

    public function run(?string $layer, Output $output): void
    {
        $debugLayers = $layer
            ? [$layer]
            : array_map(static fn (array $layer): string => $layer['name'], $this->layers);

        foreach ($debugLayers as $layer) {
            $matchedLayers = array_map(
                static fn (string $token) => (array) $token,
                $this->processor->findTokensInLayer($layer)
            );

            $output->getStyle()->table([$layer], $matchedLayers);
        }
    }
}
