<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Command;

use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Core\Analyser\TokenInLayerAnalyser;

use function array_map;

/**
 * @internal Should only be used by DebugLayerCommand
 */
final class DebugLayerRunner
{
    /**
     * @param array<array{name: string, collectors: array<array<string, string|array<string, string>>>}> $layers
     */
    public function __construct(private readonly TokenInLayerAnalyser $processor, private readonly array $layers)
    {
    }

    public function run(?string $layer, OutputInterface $output): void
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
