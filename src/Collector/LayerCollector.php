<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\AstRunner\AstMap;

class LayerCollector implements CollectorInterface
{
    public function getType(): string
    {
        return 'layer';
    }

    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        if (!isset($configuration['layer']) || !is_string($configuration['layer'])) {
            throw new InvalidArgumentException('LayerCollector needs the layer configuration.');
        }
        if (!array_key_exists($configuration['layer'], $resolutionTable)) {
            throw new InvalidArgumentException('Referenced layer in LayerCollector does not exist.');
        }

        /** @var bool $result */
        $result = $resolutionTable[$configuration['layer']];

        return $result;
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool
    {
        $key = (string) $configuration['layer'];

        return array_key_exists($key, $resolutionTable) && null !== $resolutionTable[$key];
    }
}
