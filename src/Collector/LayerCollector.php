<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\AstRunner\AstMap;

class LayerCollector implements CollectorInterface
{
    public function satisfy(
        array $configuration,
        AstMap\AstTokenReference $astTokenReference,
        AstMap $astMap,
        Registry $collectorRegistry,
        array $resolutionTable = []
    ): bool {
        if (isset($configuration['layer']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'LayerCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['layer'];
        }

        if (!isset($configuration['value']) || !is_string($configuration['value'])) {
            throw new InvalidArgumentException('LayerCollector needs the layer configuration.');
        }
        if (!array_key_exists($configuration['value'], $resolutionTable)) {
            throw new InvalidArgumentException('Referenced layer in LayerCollector does not exist.');
        }

        /** @var bool $result */
        $result = $resolutionTable[$configuration['value']];

        return $result;
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $resolutionTable): bool
    {
        /** @var array{layer?: string, value?: string} $configuration */
        if (isset($configuration['layer']) && !isset($configuration['value'])) {
            trigger_deprecation('qossmic/deptrac', '0.20.0', 'LayerCollector should use the "value" key from this version');
            $configuration['value'] = $configuration['layer'];
        }

        /** @var array{layer?: string, value: string} $configuration */
        return array_key_exists($configuration['value'], $resolutionTable) && null !== $resolutionTable[$configuration['value']];
    }
}
