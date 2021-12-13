<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

use InvalidArgumentException;
use Qossmic\Deptrac\AstRunner\AstMap;
use Qossmic\Deptrac\Configuration\ConfigurationCollector;

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
        Registry $collectorRegistry
    ): bool {
        if (!isset($configuration['layer']) || !is_string($configuration['layer'])) {
            throw new \InvalidArgumentException('LayerCollector needs the layer configuration.');
        }

        //TODO: @Incomplete: Provide collector configuration (Patrick Kusebauch @ 12.12.21)
        $configurationForCollector = ConfigurationCollector::fromArray([]);

        return $collectorRegistry->getCollector($configurationForCollector->getType())
            ->satisfy(
                $configurationForCollector->getArgs(),
                $astTokenReference,
                $astMap,
                $collectorRegistry
            );
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $alreadyResolvedLayers): bool
    {
        return in_array($configuration['layer'], $alreadyResolvedLayers, true);
    }
}
