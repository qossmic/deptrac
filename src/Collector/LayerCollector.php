<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Collector;

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
        Registry $collectorRegistry,
        array $allLayersConfiguration = []
    ): bool {
        if (!isset($configuration['layer']) || !is_string($configuration['layer'])) {
            throw new \InvalidArgumentException('LayerCollector needs the layer configuration.');
        }
        if(!array_key_exists($configuration['layer'], $allLayersConfiguration)) {
            var_dump($configuration['layer']);
            var_dump(array_keys($allLayersConfiguration));
            throw new \InvalidArgumentException('Referenced layer in LayerCollector does not exist.');
        }

        $layerConfig = $allLayersConfiguration[$configuration['layer']];

        foreach ($layerConfig->getCollectors() as $configurationForCollector) {
            if($collectorRegistry->getCollector($configurationForCollector->getType())
                ->satisfy(
                    $configurationForCollector->getArgs(),
                    $astTokenReference,
                    $astMap,
                    $collectorRegistry
                )) {
                return true;
            }
        }
        return false;
    }

    public function resolvable(array $configuration, Registry $collectorRegistry, array $alreadyResolvedLayers): bool
    {
        return in_array($configuration['layer'], $alreadyResolvedLayers, true);
    }
}
