<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac;

use SensioLabs\Deptrac\AstRunner\AstMap;
use SensioLabs\Deptrac\AstRunner\AstMap\AstClassReference;
use SensioLabs\Deptrac\Collector\Registry;
use SensioLabs\Deptrac\Configuration\Configuration;
use SensioLabs\Deptrac\Configuration\ConfigurationLayer;

class ClassNameLayerResolver implements ClassNameLayerResolverInterface
{
    protected $configuration;
    protected $astMap;
    protected $collectorRegistry;

    public function __construct(
        Configuration $configuration,
        AstMap $astMap,
        Registry $collectorRegistry
    ) {
        $this->configuration = $configuration;
        $this->astMap = $astMap;
        $this->collectorRegistry = $collectorRegistry;
    }

    /**
     * @return string[]
     */
    public function getLayersByClassName(string $className): array
    {
        $layers = [];

        foreach ($this->configuration->getLayers() as $configurationLayer) {
            foreach ($configurationLayer->getCollectors() as $configurationCollector) {
                $collector = $this->collectorRegistry->getCollector($configurationCollector->getType());

                if (!$astClassReference = $this->astMap->getClassReferenceByClassName($className)) {
                    $astClassReference = new AstClassReference($className);
                }

                if ($collector->satisfy(
                    $configurationCollector->getArgs(),
                    $astClassReference,
                    $this->astMap,
                    $this->collectorRegistry
                )
                ) {
                    $layers[$configurationLayer->getName()] = true;
                }
            }
        }

        return array_keys($layers);
    }

    public function getLayers(): array
    {
        return array_map(function (ConfigurationLayer $configurationLayer) {
            return $configurationLayer->getName();
        }, $this->configuration->getLayers());
    }
}
